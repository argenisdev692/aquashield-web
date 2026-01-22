<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => [
                'street' => $this->address,
                'street2' => $this->address_2,
                'city' => $this->city,
                'state' => $this->state,
                'zipcode' => $this->zipcode,
                'country' => $this->country,
            ],
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'has_insurance' => $this->insurance_property === 'Yes',
            'message' => $this->message,
            'sms_consent' => (bool)$this->sms_consent,
            'status' => $this->inspection_status,
            'inspection' => [
                'date' => $this->inspection_date ? $this->inspection_date->toDateString() : null,
                'time' => $this->inspection_time ? $this->inspection_time->format('H:i:s') : null,
                'confirmed' => $this->inspection_status === 'Confirmed',
                'status' => $this->inspection_status,
            ],
            'damage_detail' => $this->damage_detail,
            'intent_to_claim' => (bool)$this->intent_to_claim,
            'notes' => $this->notes,
            'owner' => $this->owner,
            'campaign' => $this->facebook_campaign,
            'follow_up_date' => $this->follow_up_date ? $this->follow_up_date->toDateString() : null,
            'additional_note' => $this->additional_note,
            'dates' => [
                'created' => $this->registration_date ? $this->registration_date->toIso8601String() : null,
                'updated' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
                'deleted' => $this->deleted_at ? $this->deleted_at->toIso8601String() : null,
            ],
            'created_at' => $this->registration_date ? $this->registration_date->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'inspection_date' => $this->inspection_date,
            'inspection_time' => $this->inspection_time,
            'inspection_status' => $this->inspection_status,
        ];
    }
} 