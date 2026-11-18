<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'symptoms',
        'diagnosis',
        'description',
        'prescription',
        'notes',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // Usage: $diagnosis->patient
    public function getPatientAttribute()
    {
        return $this->appointment?->patient;
    }

    // Usage: $diagnosis->doctor
    public function getDoctorAttribute()
    {
        return $this->appointment?->doctor;
    }
}
