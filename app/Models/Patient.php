<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'national_id',
        'phone',
        'city_id',
        'receptionist_id',
        'gender',
        'birth_date',
    ];

    /**
     * Cascade-delete the linked user account when a patient is removed.
     */
    protected static function booted(): void
    {
        static::deleting(function (self $patient) {
            $patient->user()->delete();
        });
    }

    // ---------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function receptionist(): BelongsTo
    {
        return $this->belongsTo(Receptionist::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function diagnoses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Diagnosis::class,
            Appointment::class,
            'patient_id',      // FK on appointments
            'appointment_id',  // FK on diagnoses
            'id',              // PK on patients
            'id'               // PK on appointments
        );
    }

    // ---------------------------------------------------------------------
    // Query scopes
    // ---------------------------------------------------------------------

   
    public function scopeSearch(Builder $query, string $search): Builder
    {
        $like = "%{$search}%";

        return $query->where(function (Builder $q) use ($search, $like) {
            $q->whereHas('user', function (Builder $userQuery) use ($search) {
                $userQuery->search($search);
            })
            ->orWhere('national_id', 'LIKE', $like)
            ->orWhere('phone', 'LIKE', $like);
        });
    }

   
    public function scopeForDoctor(Builder $query, int $doctorId): Builder
    {
        return $query->whereHas('appointments', function (Builder $q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        });
    }
}