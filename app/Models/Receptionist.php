<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receptionist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'city_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $receptionist) {
            $receptionist->user()->delete();
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

    public function patients()
    {
        return $this->hasMany(Patient::class, 'receptionist_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'receptionist_id');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('user', function($q) use ($search) {
            $q->search($search);
        })->orWhere('phone', 'LIKE', "%{$search}%");
    }
}