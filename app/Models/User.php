<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'activation',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activation' => 'boolean',
    ];

    // Relationships
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function physician(): HasOne
    {
        return $this->hasOne(Physician::class);
    }

    public function secretary(): HasOne
    {
        return $this->hasOne(Secretary::class);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    // Role checking with caching
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPhysician(): bool
    {
        return $this->hasRole('physician');
    }

    public function isSecretary(): bool
    {
        return $this->hasRole('secretary');
    }

    public function isPatient(): bool
    {
        return $this->hasRole('patient');
    }

    protected function hasRole(string $role): bool
    {
        static $cache = [];
        
        if (!isset($cache[$this->id][$role])) {
            $cache[$this->id][$role] = $this->$role()->exists();
        }
        
        return $cache[$this->id][$role];
    }

    // Helper methods
    public function getRoleName(): string
    {
        if ($this->isAdmin()) return 'Admin';
        if ($this->isPhysician()) return 'Physician';
        if ($this->isSecretary()) return 'Secretary';
        if ($this->isPatient()) return 'Patient';
        
        return 'Unknown';
    }

    public function getRoleAttribute()
    {
        return $this->admin 
            ?? $this->physician 
            ?? $this->secretary 
            ?? $this->patient;
    }

    public function isActive(): bool
    {
        return (bool) $this->activation;
    }

    // Query scopes
    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";
        
        return $query->where(function($q) use ($like) {
            $q->where('email', 'LIKE', $like)
              ->orWhere('name', 'LIKE', $like);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('activation', true);
    }

    public function scopePhysicians($query)
    {
        return $query->whereHas('physician');
    }

    public function scopeSecretaries($query)
    {
        return $query->whereHas('secretary');
    }

    public function scopePatients($query)
    {
        return $query->whereHas('patient');
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('admin');
    }
}