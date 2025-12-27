<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Physician extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'phone',
        'city_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $physician) {
            $physician->user()->delete();
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function todayAppointments()
    {
        return $this->appointments()
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();
    }

    public function upcomingAppointments()
    {
        return $this->appointments()
            ->where('appointment_date', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    // Search scope
    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";
        
        return $query->whereHas('user', function($q) use ($search) {
            $q->search($search);
        })->orWhere('specialization', 'LIKE', $like);
    }
}