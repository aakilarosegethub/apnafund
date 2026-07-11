<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CampaignVerificationDocumentService
{
    public const PRIVATE_DIR = 'app/private/cnic';

    /** @deprecated Previous private path — migrated on read/secure-legacy */
    public const PREVIOUS_PRIVATE_DIR = 'app/private/campaign-verification';

    public const LEGACY_PUBLIC_DIR = 'assets/universal/documents/campaign';

    public function storagePath(): string
    {
        return storage_path(self::PRIVATE_DIR);
    }

    public function previousPrivatePath(): string
    {
        return storage_path(self::PREVIOUS_PRIVATE_DIR);
    }

    public function legacyPublicPath(): string
    {
        return public_path(self::LEGACY_PUBLIC_DIR);
    }

    public function sanitizeFilename(string $filename): string
    {
        $filename = normalizeCampaignVerificationDocumentFilename($filename);

        if (! $filename) {
            abort(404);
        }

        return $filename;
    }

    public function isValidFilename(string $filename): bool
    {
        return normalizeCampaignVerificationDocumentFilename($filename) !== null;
    }

    public function findCampaignByFilename(string $filename): ?Campaign
    {
        if (! $this->isValidFilename($filename)) {
            return null;
        }

        $filename = basename($filename);

        $candidates = Campaign::query()
            ->whereNotNull('verification_documents')
            ->where('verification_documents', 'like', '%'.addcslashes($filename, '%_\\').'%')
            ->get(['id', 'user_id', 'verification_documents']);

        return $candidates->first(function (Campaign $campaign) use ($filename) {
            $documents = normalizeCampaignVerificationDocuments(
                is_array($campaign->verification_documents) ? $campaign->verification_documents : []
            );

            return in_array($filename, array_values($documents), true);
        });
    }

    public function campaignHasFile(Campaign $campaign, string $filename): bool
    {
        if (! $this->isValidFilename($filename)) {
            return false;
        }

        $filename = basename($filename);
        $documents = normalizeCampaignVerificationDocuments(
            is_array($campaign->verification_documents) ? $campaign->verification_documents : []
        );

        return in_array($filename, array_values($documents), true);
    }

    /**
     * Resolve file on disk (private storage, legacy public, or previous private path).
     */
    public function resolveAbsolutePath(string $filename): ?string
    {
        $filename = $this->sanitizeFilename($filename);

        foreach ($this->allCandidatePaths($filename) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /** @deprecated Use resolveAbsolutePath */
    public function resolvePrivateAbsolutePath(string $filename): ?string
    {
        return $this->resolveAbsolutePath($filename);
    }

    /**
     * @return list<string>
     */
    private function privateCandidatePaths(string $filename): array
    {
        return [
            $this->storagePath().DIRECTORY_SEPARATOR.$filename,
            $this->previousPrivatePath().DIRECTORY_SEPARATOR.$filename,
        ];
    }

    /**
     * @return list<string>
     */
    private function allCandidatePaths(string $filename): array
    {
        return array_merge($this->privateCandidatePaths($filename), [
            $this->legacyPublicPath().DIRECTORY_SEPARATOR.$filename,
        ]);
    }

    /**
     * Owner, or explicit campaign collaborator assignment.
     */
    public function userCanView(User $user, Campaign $campaign): bool
    {
        $userId = (int) $user->getAuthIdentifier();

        if ((int) $campaign->user_id === $userId) {
            return true;
        }

        return $campaign->collaborators()
            ->where('user_id', $userId)
            ->exists();
    }

    public function upload(UploadedFile $file, ?string $oldFilename = null): string
    {
        if ($oldFilename) {
            $this->delete($oldFilename);
        }

        if (! is_dir($this->storagePath())) {
            mkdir($this->storagePath(), 0750, true);
        }

        return fileUploader($file, $this->storagePath(), getFileSize('document'));
    }

    public function delete(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $filename = basename($filename);

        foreach ($this->allCandidatePaths($filename) as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    public function migrateLegacyFile(string $filename): bool
    {
        $filename = basename($filename);
        $privateFile = $this->storagePath().DIRECTORY_SEPARATOR.$filename;

        if (is_file($privateFile)) {
            return false;
        }

        if (! is_dir($this->storagePath())) {
            mkdir($this->storagePath(), 0750, true);
        }

        foreach ([$this->legacyPublicPath(), $this->previousPrivatePath()] as $sourceDir) {
            $sourceFile = $sourceDir.DIRECTORY_SEPARATOR.$filename;

            if (is_file($sourceFile)) {
                return rename($sourceFile, $privateFile);
            }
        }

        return false;
    }

    public function migrateAllLinkedLegacyFiles(): int
    {
        $migrated = 0;

        Campaign::query()
            ->whereNotNull('verification_documents')
            ->select(['id', 'verification_documents'])
            ->chunkById(100, function ($campaigns) use (&$migrated) {
                foreach ($campaigns as $campaign) {
                    $rawDocuments = is_array($campaign->verification_documents) ? $campaign->verification_documents : [];
                    $documents = normalizeCampaignVerificationDocuments($rawDocuments);

                    if (json_encode($documents) !== json_encode($rawDocuments)) {
                        $campaign->verification_documents = $documents;
                        $campaign->save();
                    }

                    foreach ($documents as $filename) {
                        if ($filename && $this->migrateLegacyFile((string) $filename)) {
                            $migrated++;
                        }
                    }
                }
            });

        return $migrated;
    }

    public function stream(Campaign $campaign, string $filename): BinaryFileResponse
    {
        if (! $this->campaignHasFile($campaign, $filename)) {
            abort(404);
        }

        return $this->streamFile($filename);
    }

    public function streamByFilename(string $filename): BinaryFileResponse
    {
        $campaign = $this->findCampaignByFilename($filename);

        if (! $campaign) {
            abort(404);
        }

        return $this->stream($campaign, $filename);
    }

    /**
     * Admin: serve when file exists on disk (DB link preferred but not required).
     */
    public function adminStream(string $filename): BinaryFileResponse
    {
        $filename = $this->sanitizeFilename($filename);

        $campaign = $this->findCampaignByFilename($filename);

        if ($campaign && ! $this->campaignHasFile($campaign, $filename)) {
            abort(404);
        }

        return $this->streamFile($filename);
    }

    private function streamFile(string $filename): BinaryFileResponse
    {
        $filename = $this->sanitizeFilename($filename);

        $this->migrateLegacyFile($filename);

        $absolutePath = $this->resolveAbsolutePath($filename);

        if (! $absolutePath) {
            abort(404, 'Verification document file not found on server.');
        }

        return response()->file($absolutePath, [
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
