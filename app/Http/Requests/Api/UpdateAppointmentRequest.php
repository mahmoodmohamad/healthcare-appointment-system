<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'physician_id' => ['sometimes', 'integer', 'exists:physicians,id'],
            'appointment_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'appointment_time' => ['sometimes', 'date_format:H:i'],
            'status' => ['sometimes', 'in:scheduled,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
