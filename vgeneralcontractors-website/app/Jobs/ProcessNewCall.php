<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\NewCallNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\EmailData;

class ProcessNewCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $callData;

    public function __construct(array $callData)
    {
        $this->callData = $callData;
    }

    public function handle()
    {
        try {
            $adminEmail = EmailData::where('type', 'Admin')->first();
            
            if ($adminEmail && $adminEmail->email) {
                Notification::route('mail', $adminEmail->email)
                    ->notify(new NewCallNotification($this->callData));
                
                Log::info('Call notification sent for call ID: ' . $this->callData['call_id']);
            } else {
                Log::warning('Could not find admin email address in EmailData table.');
            }
        } catch (\Exception $e) {
            Log::error('Error sending call notification: ' . $e->getMessage());
        }
    }
}