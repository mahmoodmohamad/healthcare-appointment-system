<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name_ar', 'name_en'];
    public function clinicTime()
    {

        return $this->hasMany(ClinicTime::class);
    }
    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function physicains(): BelongsToMany
    {
        return $this->belongsToMany(Physicain::class, 'clinic_physicians', 'clinic_id', 'physicain_id')->withTimestamps();;
    }
    public function patients()
    {

        return $this->hasMany(Patient::class);
    }

    public function patient(): BelongsToMany
    {

        return $this->belongsToMany(Patient::class, 'patient_clinics', 'clinic_id', 'patient_id')->withTimestamps();
    }
    public function patientClinics()
    {
        return $this->hasMany(PatientClinic::class);
    }
}
