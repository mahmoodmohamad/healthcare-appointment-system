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

    public const ROLES = ['admin', 'doctor', 'receptionist', 'patient'];

    /**
     * 'activation' اتشال من هنا عمداً: مينفعش يتغير بـ mass-assignment
     * (الـ Controllers بتستخدم forceFill). الـ DB default = true.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activation'        => 'boolean',
    ];

    protected array $roleCache = [];

    /**
     * لو العلاقة محمّلة (with()) منستخدمهاش query تاني → يمنع N+1
     * في صفحة User Management.
     */
    protected function hasRole(string $role): bool
    {
        if (! in_array($role, self::ROLES, true)) {
            return false;
        }

        return $this->roleCache[$role] ??= $this->relationLoaded($role)
            ? $this->getRelation($role) !== null
            : $this->{$role}()->exists();
    }

    // ---------- Relationships ----------
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function receptionist(): HasOne
    {
        return $this->hasOne(Receptionist::class);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    // ---------- Role checks ----------
    public function isAdmin(): bool        { return $this->hasRole('admin'); }
    public function isDoctor(): bool       { return $this->hasRole('doctor'); }
    public function isReceptionist(): bool { return $this->hasRole('receptionist'); }
    public function isPatient(): bool      { return $this->hasRole('patient'); }

    public function getRoleName(): string
    {
        if ($this->isAdmin())        return 'Admin';
        if ($this->isDoctor())       return 'Doctor';
        if ($this->isReceptionist()) return 'Receptionist';
        if ($this->isPatient())      return 'Patient';

        return 'Unknown';
    }

    public function getRoleAttribute()
    {
        return $this->admin
            ?? $this->doctor
            ?? $this->receptionist
            ?? $this->patient;
    }

    public function isActive(): bool
    {
        return (bool) $this->activation;
    }

    // ---------- Scopes ----------
    public function scopeSearch($query, string $search)
    {
        $like = "%{$search}%";

        return $query->where(function ($q) use ($like) {
            $q->where('email', 'LIKE', $like)
              ->orWhere('name', 'LIKE', $like);
        });
    }

    public function scopeActive($query)        { return $query->where('activation', true); }
    public function scopeDoctors($query)       { return $query->whereHas('doctor'); }
    public function scopeReceptionists($query) { return $query->whereHas('receptionist'); }
    public function scopePatients($query)      { return $query->whereHas('patient'); }
    public function scopeAdmins($query)        { return $query->whereHas('admin'); }
}