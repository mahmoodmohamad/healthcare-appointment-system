<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'receptionist_id',
        'appointment_date',
        'appointment_time', // ✅ Add this
        'status',
        'notes'
    ];

    protected $casts = [
        'appointment_date' => 'datetime'
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function receptionist()
    {
        return $this->belongsTo(Receptionist::class);
    }

    public function diagnosis()
    {
        return $this->hasOne(Diagnosis::class);
    }

    // ✅ Add this method
    public static function isAvailable($doctorId, $date, $time)
    {
        return !self::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_time', $time)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    // ✅ Helper to get full datetime
    public function getFullDateTimeAttribute()
    {
        if ($this->appointment_time) {
            return Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time);
        }
        return $this->appointment_date;
    }
	
	// Appointment.php
public static function book(array $data): self
{
    return DB::transaction(function () use ($data) {
        Doctor::whereKey($data['doctor_id'])->lockForUpdate()->firstOrFail();

        if (! self::isAvailable($data['doctor_id'], $data['appointment_date'], $data['appointment_time'])) {
            throw ValidationException::withMessages([
                'appointment_time' => 'This time slot is already booked.',
            ]);
        }
        return self::create($data);
    });
}
}