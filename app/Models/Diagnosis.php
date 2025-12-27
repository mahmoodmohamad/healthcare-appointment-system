<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',  
        'symptoms',
        'diagnosis',
        'prescription',
        'notes',
    ];

    // Relationships
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // ✅ للوصول للـ patient عن طريق appointment
    public function patient()
    {
        return $this->hasOneThrough(
            Patient::class,
            Appointment::class,
            'id',           // FK في appointments
            'id',           // FK في patients
            'appointment_id', // Local key في diagnoses
            'patient_id'     // Local key في appointments
        );
    }

    // ✅ للوصول للـ physician عن طريق appointment
    public function physician()
    {
        return $this->hasOneThrough(
            Physician::class,
            Appointment::class,
            'id',
            'id',
            'appointment_id',
            'physician_id'
        );
    }
}