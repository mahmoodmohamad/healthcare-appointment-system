<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'phone',
        'city_id',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $doctor) {
            $doctor->user()->delete();
        });
    }

    // ---------- Relationships ----------
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
        return $this->hasManyThrough(
            Diagnosis::class,
            Appointment::class,
            'doctor_id',       // FK on appointments
            'appointment_id',  // FK on diagnoses
            'id',
            'id'
        );
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
            ->where('status', Appointment::SCHEDULED)
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * كان فيه bug: orWhere برا أي grouping، فلما الـ scope يتركّب مع
     * where تانية (مثلاً city_id) الـ OR كان بيكسر الفلتر.
     * دلوقتي كل شروط البحث جوه where(closure) واحدة.
     */
    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";

        return $query->where(function ($q) use ($search, $like) {
            $q->whereHas('user', fn ($u) => $u->search($search))
              ->orWhere('specialization', 'LIKE', $like)
              ->orWhere('phone', 'LIKE', $like);
        });
    }
}