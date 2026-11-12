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

    protected static function booted(): void
    {
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

    /** نفس إصلاح Doctor::scopeSearch — الـ OR لازم يكون جوه group واحد */
    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";

        return $query->where(function ($q) use ($search, $like) {
            $q->whereHas('user', fn ($u) => $u->search($search))
              ->orWhere('phone', 'LIKE', $like);
        });
    }
}