<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'country_id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function physicians()
    {
        return $this->hasMany(Physician::class);
    }

    public function secretaries()
    {
        return $this->hasMany(Secretary::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }
}