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
        'physician_id',
        'secretary_id',
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

    public function physician()
    {
        return $this->belongsTo(Physician::class);
    }

    public function secretary()
    {
        return $this->belongsTo(Secretary::class);
    }

    public function diagnosis()
    {
        return $this->hasOne(Diagnosis::class);
    }

    // ✅ Add this method
    public static function isAvailable($physicianId, $date, $time)
    {
        return !self::where('physician_id', $physicianId)
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
}