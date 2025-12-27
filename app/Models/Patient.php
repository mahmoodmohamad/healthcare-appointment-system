<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'national_id',
        'phone',
        'city_id',
        'secretary_id',
		'gender',
        'birth_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $patient) {
            $patient->user()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function secretary()
    {
        return $this->belongsTo(Secretary::class);
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
        'patient_id',      // FK في appointments
        'appointment_id',  // FK في diagnoses
        'id',              // PK في patients
        'id'               // PK في appointments
    );
}


    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";
        
        return $query->whereHas('user', function($q) use ($search) {
            $q->search($search);
        })->orWhere('national_id', 'LIKE', $like)
          ->orWhere('phone', 'LIKE', $like);
    }
}