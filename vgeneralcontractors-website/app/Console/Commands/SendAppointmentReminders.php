<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\EmailData;
use Carbon\Carbon;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-appointment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email reminders for appointments scheduled for tomorrow at 9 AM Central Time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get tomorrow's date in Central Time (Texas/Chicago)
        $tomorrow = Carbon::tomorrow()->toDateString();
        
        $this->info("Sending reminders for appointments scheduled for {$tomorrow}");

        // Get all appointments scheduled for tomorrow that are confirmed
        $appointments = Appointment::where('inspection_date', $tomorrow)
            ->where('inspection_status', 'Confirmed')
            ->whereNotNull('inspection_time')
            ->get();

        // Get admin email from EmailData like ProcessNewLead does
        $adminEmailData = EmailData::where('type', 'Admin')->first();
        $adminEmail = $adminEmailData ? $adminEmailData->email : null;

        if (!$adminEmail) {
            $this->error("Admin email not found in EmailData table. Using fallback email.");
            $adminEmail = 'admin@vgeneralcontractors.com'; // Fallback from DatabaseSeeder
        }

        $count = 0;
        $errors = 0;

        $this->info("Admin email for notifications: {$adminEmail}");

        foreach ($appointments as $appointment) {
            try {
                // Send reminder to client
                Notification::route('mail', $appointment->email)
                    ->notify(new AppointmentReminderNotification($appointment, false));
                
                // Send reminder to admin
                Notification::route('mail', $adminEmail)
                    ->notify(new AppointmentReminderNotification($appointment, true));
                
                $count++;
                $this->info("Reminder sent to client: {$appointment->email} and admin: {$adminEmail}");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error sending reminder for {$appointment->email}: {$e->getMessage()}");
                Log::error("Error sending appointment reminder: " . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'email' => $appointment->email
                ]);
            }
        }

        $this->info("Process completed: {$count} reminders sent, {$errors} errors.");
        
        if ($count === 0 && $errors === 0) {
            $this->info("No appointments found scheduled for {$tomorrow}");
        }

        return 0;
    }
}
