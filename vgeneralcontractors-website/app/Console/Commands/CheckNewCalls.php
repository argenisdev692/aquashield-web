<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RetellAIService;
use App\Jobs\ProcessNewCall;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CallRecordsController;

class CheckNewCalls extends Command
{
    protected $signature = 'retell:check-calls';
    protected $description = 'Check for new calls from Retell API';

    public function handle()
    {
        $retellService = new RetellAIService();
        $calls = $retellService->listCalls();
        
        Log::info('Checking for new calls', ['total_calls' => count($calls)]);
        
        $lastCheckedTimestamp = Cache::get('last_checked_call_timestamp', 0);
        $newLastCheckedTimestamp = $lastCheckedTimestamp;
        $newCallsFound = false;
        
        foreach ($calls as $call) {
            $callTimestamp = $call['start_timestamp'] ?? 0;
            
            // Check if this is a new call (more recent than last checked timestamp)
            if ($callTimestamp > $lastCheckedTimestamp) {
                // Process the new call
                Log::info('Found new call', ['call_id' => $call['call_id']]);
                ProcessNewCall::dispatch($call);
                $newCallsFound = true;
                
                // Track the most recent call timestamp
                if ($callTimestamp > $newLastCheckedTimestamp) {
                    $newLastCheckedTimestamp = $callTimestamp;
                }
            }
        }
        
        // If we found new calls, clear the call records cache
        if ($newCallsFound) {
            $this->clearCallRecordsCache();
        }
        
        // Save the latest processed timestamp
        Cache::put('last_checked_call_timestamp', $newLastCheckedTimestamp, now()->addDays(30));
        
        $this->info('Checked for new calls. Found: ' . ($newCallsFound ? 'Yes' : 'No'));
    }
    
    /**
     * Clear the call records cache when new calls are detected
     */
    private function clearCallRecordsCache()
    {
        try {
            $callRecordsController = new CallRecordsController(app(RetellAIService::class));
            $callRecordsController->clearCallRecordsCache();
            Log::info('Call records cache cleared due to new calls');
        } catch (\Exception $e) {
            Log::error('Failed to clear call records cache: ' . $e->getMessage());
        }
    }
}