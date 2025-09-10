<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\Deposit;
use App\Constants\ManageStatus;
use Illuminate\Console\Command;

class UpdateCampaignRaisedAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:update-raised-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update campaign raised amounts from successful deposits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update campaign raised amounts...');
        
        $campaigns = Campaign::all();
        $updatedCount = 0;
        
        foreach ($campaigns as $campaign) {
            // Calculate raised amount from successful deposits
            $raisedAmount = Deposit::where('campaign_id', $campaign->id)
                ->where('status', ManageStatus::PAYMENT_SUCCESS)
                ->sum('amount');
            
            // Update the campaign if the calculated amount is different
            if ($campaign->raised_amount != $raisedAmount) {
                $campaign->update(['raised_amount' => $raisedAmount]);
                $updatedCount++;
                
                $this->line("Updated campaign '{$campaign->name}': {$campaign->raised_amount} -> {$raisedAmount}");
            }
        }
        
        $this->info("Updated {$updatedCount} campaigns successfully.");
        
        return Command::SUCCESS;
    }
}