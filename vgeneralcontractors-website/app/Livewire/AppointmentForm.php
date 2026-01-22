<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Jobs\ProcessNewLead;
use App\Services\FacebookConversionApi;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\TransactionService;
use Revolution\Google\Sheets\Facades\Sheets;
use Illuminate\Support\Facades\Log;
use Throwable;

class AppointmentForm extends Component
{
    // Form fields
    // public $name; // Removed
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $address;
    public $address_2;
    public $city;
    public $state;
    public $zipcode;
    public $country;
    // public $insurance; // Removed
    public $insurance_property;
    public $message;
    public $sms_consent = false;

    // Component state
    public $success = false;
    public $show = false;

    // Inyectar el servicio
    protected TransactionService $transactionService;

    protected $rules = [
        // 'name' => 'required|min:2', // Removed
        'first_name' => 'required|min:2',
        'last_name' => 'required|min:2',
        'phone' => 'required|regex:/^\(\d{3}\)\s\d{3}-\d{4}$/',
        'email' => 'required|email',
        'address' => 'required|min:5',
        'address_2' => 'nullable',
        'city' => 'required',
        'state' => 'required',
        'zipcode' => 'required|regex:/^\d{5}(-\d{4})?$/',
        'country' => 'required',
        // 'insurance' => 'required|in:yes,no', // Removed
        'insurance_property' => 'required|in:yes,no',
        'message' => 'nullable|min:5',
        'sms_consent' => 'boolean'
    ];

    // Fields to reset after successful submission or modal close
    private function getResetFields()
    {
        return [
            'first_name', 'last_name', 'phone', 'email',
            'address', 'address_2', 'city', 'state', 'zipcode', 'country',
            'insurance_property', 'message', 'sms_consent'
        ];
    }

    protected $listeners = [
        'openAppointmentModal' => 'open',
        'closeAppointmentModal' => 'closeModal',
        'address-selected' => 'handleAddressSelected'
    ];

    // Constructor para inyección de dependencias
    public function boot(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function open()
    {
        $this->show = true;
        $this->dispatchBrowserEvent('open-appointment-modal');
    }

    public function closeModal()
    {
        $this->show = false;
        $this->dispatchBrowserEvent('close-appointment-modal');
        // Reset fields and success state
        $this->reset(array_merge($this->getResetFields(), ['success']));
        $this->resetValidation(); // Also reset validation errors
    }

    public function submit()
    {
        // Forzar actualización de todos los campos
        $this->resetValidation();
        
        $validatedData = $this->validate(); // Validate first

        try {
            // ---- Usar el TransactionService ----
            $appointment = $this->transactionService->run(
                // 1. Operaciones de Base de Datos y relacionadas que deben revertirse juntas
                function () {
                    // Crear Appointment
                    $newAppointment = Appointment::create([
                        'uuid' => Str::uuid(),
                        // 'name' => $this->name, // Removed
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'phone' => $this->phone,
                        'email' => $this->email,
                        'address' => $this->address,
                        'address_2' => $this->address_2,
                        'city' => $this->city,
                        'state' => $this->state,
                        'zipcode' => $this->zipcode,
                        'country' => $this->country,
                        // 'insurance' => $this->insurance === 'yes' ? 'Si' : 'No', // Removed
                        'insurance_property' => $this->insurance_property === 'yes' ? 'Yes' : 'No',
                        'message' => $this->message,
                        'sms_consent' => $this->sms_consent,
                        'registration_date' => Carbon::now(),
                        'inspection_status' => 'Pending',
                    ]);

                    // --- Preparar datos para Google Sheets ---
                    $sheetName = 'Leads'; // Asegúrate que este es el nombre correcto de tu hoja/pestaña
                    $registrationDate = Carbon::now()->toDateTimeString(); // Formato YYYY-MM-DD HH:MM:SS

                    // Construir la dirección completa
                    $fullAddress = trim($this->address . ' ' . $this->address_2) . ', ' . $this->city . ', ' . $this->state . ' ' . $this->zipcode;

                    // Mapear los datos a las columnas de Google Sheet en el orden correcto
                    $values = [
                        $this->first_name . ' ' . $this->last_name, // Nombre
                        "'" . $this->phone,                         // Telefono (Prefijo con ' para tratar como texto)
                        $fullAddress,                               // Dirección completa
                        $this->email,                               // Email
                        $this->insurance_property === 'yes' ? 'Si' : 'No', // Tiene_Seguro (Traducido a español como en el ejemplo)
                        $this->state,                               // Estado (US state code)
                        $registrationDate,                          // Fecha_Registro
                        null,                                       // Fecha de Inspección (No disponible en el form)
                        null,                                       // Hora de Inspección (No disponible en el form)
                        null,                                       // Inspección_Confirmada (No disponible en el form)
                        $this->message,                             // Notas
                        null,                                       // Propietario (No disponible)
                        null,                                       // Detalle_del_Daño (No disponible)
                        null,                                       // Intención_Reclamar (No disponible)
                        null,                                       // Campaña_Facebook (No disponible directamente)
                        null,                                       // Fecha_de_Seguimiento (No disponible)
                        null,                                       // Nota Adicional (Usar null o duplicar $this->message si se quiere)
                        'Pending',                                  // Estatus_Inspeccion (Valor inicial)
                        $newAppointment->uuid                       // Añadir UUID al final como referencia (opcional)
                    ];

                    // Escribir en Google Sheets
                    Sheets::spreadsheet(config('services.google.sheet_id'))
                          ->sheet($sheetName)
                          ->append([$values]); // append espera un array de filas (un array anidado)

                    Log::info('Lead successfully added to Google Sheet within transaction.', ['email' => $this->email, 'sheet_id' => config('services.google.sheet_id'), 'sheet_name' => $sheetName]);

                    return $newAppointment; // Devolver el resultado (el Appointment creado)
                },
                // 2. Acciones Post-Commit (si la transacción tiene éxito)
                function ($createdAppointment) { // Recibe el resultado de la closure anterior
                    Log::info('Transaction committed for Appointment. Running post-commit actions.', ['appointment_uuid' => $createdAppointment->uuid]);
                    ProcessNewLead::dispatch($createdAppointment); // Despachar Job

                    // Track Facebook event
                    $fbApi = app(FacebookConversionApi::class);
                    $fbApi->lead([
                        'email' => $createdAppointment->email,
                        'phone' => $createdAppointment->phone,
                        'first_name' => $createdAppointment->first_name,
                        'last_name' => $createdAppointment->last_name,
                        'address' => $createdAppointment->address,
                        'address_2' => $createdAppointment->address_2,
                        'city' => $createdAppointment->city,
                        'state' => $createdAppointment->state,
                        'zip_code' => $createdAppointment->zipcode,
                        'country' => $createdAppointment->country,
                    ], [
                        'content_name' => 'Appointment Request',
                        'content_type' => 'Roof Inspection Service',
                        'content_id' => 'appointment_request',
                        'event_id' => 'appointment_' . time() . '_' . substr(md5($createdAppointment->email), 0, 8),
                    ]);

                    // Resetear campos y mostrar éxito (aquí porque dependen del éxito total)
                    $this->reset($this->getResetFields());
                    session()->flash('appointment_success', 'Your appointment request has been submitted.');
                    
                    // Redirect to confirmation page
                    return redirect()->route('appointment.confirmation');
                },
                // 3. Acciones en Error (antes del rollback)
                function (Throwable $e) {
                    Log::error('Error occurred during AppointmentForm transaction, before rollback.', [
                         'error_message' => $e->getMessage(),
                         'email' => $this->email ?? 'N/A'
                    ]);
                    // Puedes añadir lógica específica aquí si es necesario
                    // $this->success = false; // No longer needed
                }
            );
            // ---- Fin uso del TransactionService ----

        } catch (Throwable $e) {
            // El TransactionService ya hizo rollback y logueó el error principal.
            // Aquí solo manejamos la notificación al usuario.
             session()->flash('error', 'There was an error submitting your request. Please try again. Error details logged.');
             // No necesitas resetear aquí, ya que el estado probablemente no cambió o fue revertido.
        }
    }

    // Handle Google Maps place selection
    public function handleAddressSelected($data)
    {
        $this->address = $data['address'];
        $this->city = $data['city'];
        $this->state = $data['state'];
        $this->zipcode = $data['zipcode'];
        $this->country = $data['country'];
        
        // Reset validation errors for these fields
        $this->resetValidation(['address', 'city', 'state', 'zipcode', 'country']);
    }

    public function render()
    {
        return view('livewire.appointment-form');
    }
}
