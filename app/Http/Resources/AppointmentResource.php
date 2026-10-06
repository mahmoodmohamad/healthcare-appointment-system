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
            'doctor_id' => $this->doctor_id,
            'receptionist_id' => $this->receptionist_id,
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
            'doctor' => $this->whenLoaded('doctor', function () {
                return [
                    'id' => $this->doctor->id,
                    'name' => optional($this->doctor->user)->name,
                    'specialization' => $this->doctor->specialization,
                ];
            }),
            'receptionist' => $this->whenLoaded('receptionist', function () {
                return [
                    'id' => $this->receptionist->id,
                    'name' => optional($this->receptionist->user)->name,
                ];
            }),
            'diagnosis' => $this->whenLoaded('diagnosis'),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
