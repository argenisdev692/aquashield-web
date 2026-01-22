<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Services\RetellAIService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RetellFollowUpCalls extends Command
{
    protected $signature = 'retell:follow-up-calls {--time=}';
    protected $description = 'Make follow-up calls to leads with status New (2 times per day for 5 days max)';

    protected $retellService;

    public function __construct(RetellAIService $retellService)
    {
        parent::__construct();
        $this->retellService = $retellService;
    }

    public function handle()
    {
        $currentTime = $this->option('time') ?: now()->format('H:i');
        $currentHour = (int) explode(':', $currentTime)[0];
        
        // Solo ejecutar a las 9 AM (09:00) y 4 PM (16:00)
        if (!in_array($currentHour, [9, 16])) {
            $this->info('Follow-up calls only run at 9 AM and 4 PM');
            return 0;
        }

        $callPeriod = $currentHour === 9 ? 'morning' : 'afternoon';
        $this->info("Starting {$callPeriod} follow-up calls at {$currentTime}");

        // Buscar leads con status "New" que necesiten seguimiento
        $leads = $this->getLeadsForFollowUp();

        if ($leads->isEmpty()) {
            $this->info('No leads found for follow-up calls');
            return 0;
        }

        $callCount = 0;
        $successCount = 0;
        $failCount = 0;

        foreach ($leads as $lead) {
            try {
                // Verificar si ya se hicieron las 10 llamadas máximas (2 por día x 5 días)
                if ($this->hasReachedMaxCalls($lead)) {
                    $this->info("Lead {$lead->id} has reached maximum follow-up calls (10 calls)");
                    continue;
                }

                // Verificar si ya se llamó en este período hoy
                if ($this->alreadyCalledToday($lead, $callPeriod)) {
                    $this->info("Lead {$lead->id} already called this {$callPeriod}");
                    continue;
                }

                // Hacer la llamada
                $result = $this->makeFollowUpCall($lead);
                
                if ($result['success']) {
                    $successCount++;
                    $this->info("✓ Called lead {$lead->id} - {$lead->first_name} {$lead->last_name} ({$lead->phone})");
                } else {
                    $failCount++;
                    $this->error("✗ Failed to call lead {$lead->id}: {$result['message']}");
                }
                
                $callCount++;
                
                // Pausa de 10 segundos entre llamadas para no sobrecargar
                if ($callCount < $leads->count()) {
                    sleep(10);
                }

            } catch (\Exception $e) {
                $failCount++;
                $this->error("Error calling lead {$lead->id}: {$e->getMessage()}");
                Log::error('Retell follow-up call error', [
                    'lead_id' => $lead->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("Follow-up calls completed:");
        $this->info("- Total leads processed: {$callCount}");
        $this->info("- Successful calls: {$successCount}");
        $this->info("- Failed calls: {$failCount}");

        Log::info('Retell follow-up calls completed', [
            'period' => $callPeriod,
            'total_processed' => $callCount,
            'successful' => $successCount,
            'failed' => $failCount
        ]);

        return 0;
    }

    /**
     * Obtener leads que necesitan seguimiento
     */
    private function getLeadsForFollowUp()
    {
        return Appointment::where('status_lead', 'New')
            ->whereNull('inspection_date') // Solo leads sin cita agendada
            ->where('created_at', '>=', now()->subDays(5)) // Últimos 5 días
            ->where('created_at', '<=', now()->subHours(2)) // Al menos 2 horas después de creado
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Verificar si el lead ya alcanzó el máximo de llamadas (10 total)
     */
    private function hasReachedMaxCalls($lead)
    {
        $followUpCalls = json_decode($lead->follow_up_calls ?? '[]', true);
        return count($followUpCalls) >= 10; // 2 llamadas x 5 días = 10 máximo
    }

    /**
     * Verificar si ya se llamó hoy en este período
     */
    private function alreadyCalledToday($lead, $callPeriod)
    {
        $followUpCalls = json_decode($lead->follow_up_calls ?? '[]', true);
        $today = now()->format('Y-m-d');
        
        foreach ($followUpCalls as $call) {
            if ($call['date'] === $today && $call['period'] === $callPeriod) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Realizar llamada de seguimiento
     */
    private function makeFollowUpCall($lead)
    {
        try {
            // Detectar idioma preferido basado en datos del lead
            $preferredLanguage = $this->detectPreferredLanguage($lead);

            // Preparar datos para la llamada
            $callData = [
                'from_number' => env('RETELL_PHONE_NUMBER'), // Número de la empresa
                'to_number' => $this->formatPhoneNumber($lead->phone),
                'agent_id' => env('RETELL_AGENT_ID'),
                'metadata' => [
                    'type' => 'follow_up',
                    'lead_id' => $lead->id,
                    'customer_name' => $lead->first_name . ' ' . $lead->last_name,
                    'customer_address' => $lead->address . ', ' . $lead->city . ', ' . $lead->state,
                    'lead_source' => $lead->lead_source,
                    'created_date' => $lead->created_at->format('Y-m-d'),
                    'follow_up_attempt' => $this->getFollowUpAttemptNumber($lead) + 1,
                    'preferred_language' => $preferredLanguage,
                    'customer_phone' => $lead->phone,
                    'customer_email' => $lead->email
                ]
            ];

            // Hacer la llamada usando RetellAIService
            $response = $this->retellService->createCall($callData);

            if ($response && isset($response['call_id'])) {
                // Registrar la llamada de seguimiento en el lead
                $this->recordFollowUpCall($lead, $response['call_id']);
                
                return [
                    'success' => true,
                    'call_id' => $response['call_id'],
                    'message' => 'Call initiated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to initiate call - No call ID returned'
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Registrar llamada de seguimiento en el lead
     */
    private function recordFollowUpCall($lead, $callId)
    {
        $followUpCalls = json_decode($lead->follow_up_calls ?? '[]', true);
        $currentTime = now();
        $callPeriod = $currentTime->hour === 9 ? 'morning' : 'afternoon';

        $followUpCalls[] = [
            'call_id' => $callId,
            'date' => $currentTime->format('Y-m-d'),
            'time' => $currentTime->format('H:i:s'),
            'period' => $callPeriod,
            'attempt' => count($followUpCalls) + 1,
            'timezone' => 'America/Chicago'
        ];

        $lead->follow_up_calls = json_encode($followUpCalls);
        $lead->save();

        Log::info('Follow-up call recorded', [
            'lead_id' => $lead->id,
            'call_id' => $callId,
            'attempt' => count($followUpCalls),
            'period' => $callPeriod
        ]);
    }

    /**
     * Obtener número de intento de seguimiento
     */
    private function getFollowUpAttemptNumber($lead)
    {
        $followUpCalls = json_decode($lead->follow_up_calls ?? '[]', true);
        return count($followUpCalls);
    }

    /**
     * Formatear número de teléfono para Retell AI
     */
    private function formatPhoneNumber($phone)
    {
        // Remover todos los caracteres no numéricos excepto el +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Si no tiene código de país, agregar +1
        if (!str_starts_with($cleaned, '+')) {
            if (strlen($cleaned) === 10) {
                $cleaned = '+1' . $cleaned;
            } elseif (strlen($cleaned) === 11 && str_starts_with($cleaned, '1')) {
                $cleaned = '+' . $cleaned;
            }
        }
        
        return $cleaned;
    }

    /**
     * Detectar idioma preferido del cliente
     */
    private function detectPreferredLanguage($lead)
    {
        // Verificar si hay notas que indiquen el idioma
        if ($lead->notes) {
            $notes = strtolower($lead->notes);
            if (strpos($notes, 'english') !== false || strpos($notes, 'inglés') !== false) {
                return 'english';
            }
            if (strpos($notes, 'spanish') !== false || strpos($notes, 'español') !== false) {
                return 'spanish';
            }
        }

        // Detectar por nombre (heurística básica)
        $firstName = strtolower($lead->first_name);
        $lastName = strtolower($lead->last_name);
        
        // Nombres comunes en español
        $spanishNames = [
            'juan', 'maría', 'carlos', 'ana', 'luis', 'carmen', 'josé', 'francisco',
            'antonio', 'manuel', 'david', 'miguel', 'alejandro', 'pedro', 'pablo',
            'gonzález', 'rodríguez', 'hernández', 'lópez', 'martínez', 'pérez',
            'garcía', 'sánchez', 'ramírez', 'torres', 'flores', 'rivera', 'gómez'
        ];

        foreach ($spanishNames as $name) {
            if (strpos($firstName, $name) !== false || strpos($lastName, $name) !== false) {
                return 'spanish';
            }
        }

        // Por defecto, usar español para el área de Houston (alta población hispana)
        return 'spanish';
    }
} 