<?php

namespace App\Services;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Log;

class FacebookConversionApi
{
    protected $pixelId;
    protected $accessToken;
    protected $api;

    public function __construct()
    {
        $this->pixelId = config('services.facebook.pixel_id');
        $this->accessToken = config('services.facebook.access_token');
        
        // Inicializar la API
        Api::init(null, null, $this->accessToken, false);
        
        // Habilitar logger para desarrollo (comentar en producción)
        if (config('app.debug')) {
            $this->api = Api::instance();
            $this->api->setLogger(new CurlLogger());
        }
    }

    /**
     * Enviar evento a la API de Conversiones
     * 
     * @param string $eventName Nombre del evento (PageView, Lead, etc)
     * @param array $userData Datos del usuario
     * @param array $customData Datos adicionales del evento
     * @return mixed
     */
    public function sendEvent($eventName, $userData = [], $customData = [])
    {
        try {
            // Crear datos de usuario
            $user = new UserData();
            
            // Aplicar hash a los datos del usuario para privacidad
            if (!empty($userData['email'])) {
                $user->setEmail(hash('sha256', strtolower(trim($userData['email']))));
            }
            
            if (!empty($userData['phone'])) {
                $phone = preg_replace('/[^0-9]/', '', $userData['phone']);
                $user->setPhone(hash('sha256', $phone));
            }
            
            if (!empty($userData['first_name'])) {
                $user->setFirstName(hash('sha256', strtolower(trim($userData['first_name']))));
            }
            
            if (!empty($userData['last_name'])) {
                $user->setLastName(hash('sha256', strtolower(trim($userData['last_name']))));
            }
            
            if (!empty($userData['zip_code'])) {
                $user->setZipCode(hash('sha256', trim($userData['zip_code'])));
            }
            
            if (!empty($userData['city'])) {
                $user->setCity(hash('sha256', strtolower(trim($userData['city']))));
            }
            
            if (!empty($userData['state'])) {
                $user->setState(hash('sha256', strtolower(trim($userData['state']))));
            }
            
            // IP y User Agent
            $user->setClientIpAddress(request()->ip());
            $user->setClientUserAgent(request()->userAgent());
            
            // Crear objeto de datos personalizados
            $eventCustomData = new CustomData();
            
            if (!empty($customData['value'])) {
                $eventCustomData->setValue($customData['value']);
            }
            
            if (!empty($customData['currency'])) {
                $eventCustomData->setCurrency($customData['currency']);
            }
            
            if (!empty($customData['content_type'])) {
                $content = (new Content())
                    ->setTitle(!empty($customData['content_name']) ? $customData['content_name'] : 'Roofing Service')
                    ->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
                
                if (!empty($customData['content_id'])) {
                    $eventCustomData->setContentIds([$customData['content_id']]);
                }
                    
                $eventCustomData->setContents([$content]);
            }
            
            // Crear evento
            $event = (new Event())
                ->setEventName($eventName)
                ->setEventTime(time())
                ->setEventSourceUrl(request()->fullUrl())
                ->setUserData($user)
                ->setCustomData($eventCustomData)
                ->setActionSource(ActionSource::WEBSITE);
            
            // Opcional: añadir ID de evento único para deduplicación
            if (!empty($customData['event_id'])) {
                $event->setEventId($customData['event_id']);
            }
            
            // Crear y ejecutar la solicitud
            $request = (new EventRequest($this->pixelId))
                ->setEvents([$event]);
                
            $response = $request->execute();
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Facebook Conversion API Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Rastrear vista de página
     */
    public function pageView($userData = [])
    {
        return $this->sendEvent('PageView', $userData);
    }
    
    /**
     * Rastrear lead
     */
    public function lead($userData = [], $customData = [])
    {
        return $this->sendEvent('Lead', $userData, $customData);
    }
    
    /**
     * Rastrear contacto (para formularios de contacto)
     */
    public function contact($userData = [])
    {
        return $this->sendEvent('Contact', $userData);
    }
    
    /**
     * Rastrear programación de cita (ideal para presupuestos de roofing)
     */
    public function schedule($userData = [], $customData = [])
    {
        return $this->sendEvent('Schedule', $userData, $customData);
    }
} 