<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Appointment;
use App\Models\CompanyData;
use App\Models\EmailData;
use App\Notifications\AppointmentRejectionNotification;
use App\Notifications\AdminRejectionNotification;
use App\Notifications\InfoRejectionNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ProcessRejectionNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointmentIds;
    protected $noContact;
    protected $noInsurance;
    protected $otherReason;

    /**
     * Create a new job instance.
     *
     * @param array $appointmentIds
     * @param bool $noContact
     * @param bool $noInsurance
     * @param string|null $otherReason
     * @return void
     */
    public function __construct(array $appointmentIds, bool $noContact, bool $noInsurance, ?string $otherReason)
    {
        $this->appointmentIds = $appointmentIds;
        $this->noContact = $noContact;
        $this->noInsurance = $noInsurance;
        $this->otherReason = $otherReason;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Obtener información de la compañía
        $companyData = CompanyData::first();
        
        if (!$companyData) {
            Log::warning("No se encontró información de la compañía para las notificaciones de rechazo");
        }
        
        $processed = 0;
        $errors = 0;
        
        Log::info("Iniciando procesamiento de notificaciones de rechazo", [
            'total_appointments' => count($this->appointmentIds)
        ]);
        
        foreach ($this->appointmentIds as $appointmentId) {
            try {
                // Use withTrashed() to find the appointment even if it's been soft-deleted
                $appointment = Appointment::withTrashed()->where('uuid', $appointmentId)->first();
                
                if (!$appointment) {
                    Log::warning("Cita con UUID {$appointmentId} no encontrada al procesar rechazo.");
                    $errors++;
                    continue;
                }
                
                // Omitir si no hay correo electrónico
                if (empty($appointment->email)) {
                    Log::warning("Cita con UUID {$appointmentId} no tiene dirección de correo. Omitiendo notificación de rechazo.");
                    continue;
                }
                
                // 1. Enviar notificación al cliente
                Notification::route('mail', $appointment->email)
                    ->notify(new AppointmentRejectionNotification(
                        $appointment, 
                        $this->noContact, 
                        $this->noInsurance, 
                        $this->otherReason,
                        $companyData
                    ));
                
                // 2. Enviar notificación al administrador
                try {
                    // Use our helper to verify admin email
                    $adminEmailVerification = \App\Helpers\EmailHelper::verifyAdminEmail();
                    
                    if ($adminEmailVerification['isValid']) {
                        Log::info("Enviando notificación de rechazo al administrador: {$adminEmailVerification['email']}");
                        
                        Notification::route('mail', $adminEmailVerification['email'])
                            ->notify(new AdminRejectionNotification(
                                $appointment, 
                                $this->noContact, 
                                $this->noInsurance, 
                                $this->otherReason,
                                $companyData
                            ));
                        Log::info("Notificación de rechazo enviada al administrador para la cita UUID: {$appointmentId}");
                    } else {
                        $emailFound = $adminEmailVerification['exists'] ? 'encontrado pero inválido' : 'no encontrado';
                        $emailValue = $adminEmailVerification['email'] ?? 'No hay email';
                        Log::warning("Email de administrador {$emailFound}. Omitiendo notificación de rechazo al administrador. Email: {$emailValue}");
                    }
                } catch (\Exception $e) {
                    Log::error("Error al enviar notificación de rechazo al administrador para la cita UUID: {$appointmentId} - Error: {$e->getMessage()}", [
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                // 3. Enviar notificación al correo Info
                try {
                    // Use our helper to verify info email
                    $infoEmailVerification = \App\Helpers\EmailHelper::verifyInfoEmail();
                    
                    if ($infoEmailVerification['isValid']) {
                        Log::info("Enviando notificación de rechazo al correo Info: {$infoEmailVerification['email']}");
                        
                        Notification::route('mail', $infoEmailVerification['email'])
                            ->notify(new InfoRejectionNotification(
                                $appointment, 
                                $this->noContact, 
                                $this->noInsurance, 
                                $this->otherReason,
                                $companyData
                            ));
                        Log::info("Notificación de rechazo enviada al correo Info para la cita UUID: {$appointmentId}");
                    } else {
                        $emailFound = $infoEmailVerification['exists'] ? 'encontrado pero inválido' : 'no encontrado';
                        $emailValue = $infoEmailVerification['email'] ?? 'No hay email';
                        Log::warning("Email de Info {$emailFound}. Omitiendo notificación de rechazo al correo Info. Email: {$emailValue}");
                    }
                } catch (\Exception $e) {
                    Log::error("Error al enviar notificación de rechazo al correo Info para la cita UUID: {$appointmentId} - Error: {$e->getMessage()}", [
                        'exception' => get_class($e),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                // Actualizar estado de la cita a rechazada
                $appointment->status_lead = 'Declined';
                $appointment->inspection_status = 'Declined';
                $appointment->notes = $appointment->notes . "\n\n" . date('Y-m-d H:i:s') . " - Notificación de rechazo enviada. Razones: " 
                    . ($this->noContact ? "No fue posible contactar. " : "")
                    . ($this->noInsurance ? "No tiene seguro de propiedad. " : "")
                    . ($this->otherReason ? "Otro: {$this->otherReason}" : "");
                $appointment->save();
                
                Log::info("Notificación de rechazo enviada con éxito para la cita UUID: {$appointmentId}");
                $processed++;
            } catch (\Exception $e) {
                Log::error("Error al enviar notificación de rechazo para la cita UUID: {$appointmentId} - Error: {$e->getMessage()}");
                // Continuar procesando otras citas aunque una falle
                $errors++;
            }
        }
        
        // Log de resumen al finalizar todas las notificaciones
        $adminEmailVerification = \App\Helpers\EmailHelper::verifyAdminEmail();
        $infoEmailVerification = \App\Helpers\EmailHelper::verifyInfoEmail();
        
        Log::info("Procesamiento de notificaciones de rechazo completado", [
            'total' => count($this->appointmentIds),
            'processed' => $processed,
            'errors' => $errors,
            'reasons' => [
                'no_contact' => $this->noContact,
                'no_insurance' => $this->noInsurance,
                'has_other_reason' => !empty($this->otherReason),
            ],
            'admin_email_found' => $adminEmailVerification['exists'],
            'admin_email' => $adminEmailVerification['email'],
            'admin_email_valid' => $adminEmailVerification['isValid'],
            'info_email_found' => $infoEmailVerification['exists'],
            'info_email' => $infoEmailVerification['email'],
            'info_email_valid' => $infoEmailVerification['isValid']
        ]);
    }
} 