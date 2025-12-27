<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $admin) {
            $admin->user()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->whereHas('user', function($q) use ($search) {
            $q->search($search);
        });
    }
}