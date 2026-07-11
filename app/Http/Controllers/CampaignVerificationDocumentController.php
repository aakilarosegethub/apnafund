<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use App\Services\CampaignVerificationDocumentService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CampaignVerificationDocumentController extends Controller
{
    public function __construct(
        private readonly CampaignVerificationDocumentService $documents
    ) {}

    /**
     * GET /user/cnic/{id} or GET /api/cnic/{id}
     */
    public function showByDocumentId(Request $request, string $id): BinaryFileResponse
    {
        $user = $this->resolveAuthenticatedUser($request);

        if (! $user) {
            $this->denyUnauthenticated($request);
        }

        $filename = normalizeCampaignVerificationDocumentFilename($id);

        if (! $filename) {
            abort(404);
        }

        $campaign = $this->documents->findCampaignByFilename($filename);

        if (! $campaign) {
            abort(404);
        }

        if (! $this->documents->userCanView($user, $campaign)) {
            abort(403, 'You do not have permission to view this document.');
        }

        return $this->documents->stream($campaign, $filename);
    }

    /**
     * GET /admin/cnic/{id}
     */
    public function adminShowByDocumentId(Request $request, string $id): BinaryFileResponse
    {
        if (! auth('admin')->check()) {
            abort(403, 'Admin authentication required.');
        }

        $filename = normalizeCampaignVerificationDocumentFilename($id);

        if (! $filename) {
            abort(404);
        }

        return $this->documents->adminStream($filename);
    }

    /** @deprecated Use showByDocumentId */
    public function show(Request $request, int $campaign, string $filename): BinaryFileResponse
    {
        $user = $this->resolveAuthenticatedUser($request);

        if (! $user) {
            $this->denyUnauthenticated($request);
        }

        $campaignModel = Campaign::findOrFail($campaign);

        if (! $this->documents->userCanView($user, $campaignModel)) {
            abort(403, 'You do not have permission to view this document.');
        }

        return $this->documents->stream($campaignModel, $filename);
    }

    /** @deprecated Use adminShowByDocumentId */
    public function adminShow(Request $request, int $campaign, string $filename): BinaryFileResponse
    {
        if (! auth('admin')->check()) {
            abort(403, 'Admin authentication required.');
        }

        $campaignModel = Campaign::findOrFail($campaign);

        return $this->documents->stream($campaignModel, $filename);
    }

    private function resolveAuthenticatedUser(Request $request): ?User
    {
        if ($request->is('api/*')) {
            $user = $request->user('sanctum');

            return $user instanceof User ? $user : null;
        }

        $user = auth('web')->user();

        return $user instanceof User ? $user : null;
    }

    private function denyUnauthenticated(Request $request): never
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            abort(401, 'Authentication required.');
        }

        throw new AuthenticationException('Authentication required.');
    }
}
