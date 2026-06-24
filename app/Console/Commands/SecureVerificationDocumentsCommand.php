<?php

namespace App\Console\Commands;

use App\Services\CampaignVerificationDocumentService;
use Illuminate\Console\Command;

class SecureVerificationDocumentsCommand extends Command
{
    protected $signature = 'verification-documents:secure-legacy';

    protected $description = 'Move campaign CNIC/verification documents from public web root to storage/app/private/cnic';

    public function handle(CampaignVerificationDocumentService $documents): int
    {
        $migrated = $documents->migrateAllLinkedLegacyFiles();

        $this->info("Moved {$migrated} verification document(s) to private storage.");

        return self::SUCCESS;
    }
}
