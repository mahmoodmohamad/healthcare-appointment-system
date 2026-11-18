<?php

declare(strict_types=1);

namespace App\Http\Requests\Doctor;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAppointmentsRequest extends FormRequest
{
    public const FILTER_TODAY = 'today';
    public const FILTER_UPCOMING = 'upcoming';
    public const FILTER_PAST = 'past';

    public function authorize(): bool
    {
        return $this->user()?->isDoctor() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'nullable',
                Rule::in([
                    Appointment::SCHEDULED,
                    Appointment::COMPLETED,
                    Appointment::CANCELLED,
                ]),
            ],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'filter' => [
                'nullable',
                Rule::in([
                    self::FILTER_TODAY,
                    self::FILTER_UPCOMING,
                    self::FILTER_PAST,
                ]),
            ],
        ];
    }
}