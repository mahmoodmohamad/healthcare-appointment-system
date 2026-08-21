<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'physician_id' => $this->physician_id,
            'secretary_id' => $this->secretary_id,
            'appointment_date' => optional($this->appointment_date)->format('Y-m-d'),
            'appointment_time' => $this->appointment_time,
            'status' => $this->status,
            'notes' => $this->notes,
            'patient' => $this->whenLoaded('patient', function () {
                return [
                    'id' => $this->patient->id,
                    'name' => optional($this->patient->user)->name,
                    'national_id' => $this->patient->national_id,
                ];
            }),
            'physician' => $this->whenLoaded('physician', function () {
                return [
                    'id' => $this->physician->id,
                    'name' => optional($this->physician->user)->name,
                    'specialization' => $this->physician->specialization,
                ];
            }),
            'secretary' => $this->whenLoaded('secretary', function () {
                return [
                    'id' => $this->secretary->id,
                    'name' => optional($this->secretary->user)->name,
                ];
            }),
            'diagnosis' => $this->whenLoaded('diagnosis'),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
