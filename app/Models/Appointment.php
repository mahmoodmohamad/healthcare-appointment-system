<?php

namespace App\Models;

use App\Exceptions\InvalidAppointmentTransition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class Appointment extends Model
{
    use HasFactory;

    const SCHEDULED = 'scheduled';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';

    const TRANSITIONS = [
        self::SCHEDULED => [self::COMPLETED, self::CANCELLED],
        self::COMPLETED => [],
        self::CANCELLED => [],
    ];

    protected $fillable = [
        'patient_id', 'doctor_id', 'receptionist_id',
        'appointment_date', 'appointment_time', 'status', 'notes',
    ];

    protected $casts = ['appointment_date' => 'datetime'];

    // Relationships
    public function patient()      { return $this->belongsTo(Patient::class); }
    public function doctor()       { return $this->belongsTo(Doctor::class); }
    public function receptionist() { return $this->belongsTo(Receptionist::class); }
    public function diagnosis()    { return $this->hasOne(Diagnosis::class); }

    // ---------- Lifecycle ----------
    public function canTransitionTo(string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$this->status] ?? [], true);
    }

    public function transitionTo(string $to): void
    {
        if (! $this->canTransitionTo($to)) {
            throw InvalidAppointmentTransition::make($this->status, $to);
        }
        $this->update(['status' => $to]);
    }

    public function complete(): void { $this->transitionTo(self::COMPLETED); }
    public function cancel(): void   { $this->transitionTo(self::CANCELLED); }

    public function reschedule(int $doctorId, string $date, string $time): void
    {
        if ($this->status !== self::SCHEDULED) {
            throw InvalidAppointmentTransition::make($this->status, 'rescheduled');
        }

        // فحص ودّي للرسالة، والـ unique index هو الحماية الحقيقية
        if (! static::isAvailable($doctorId, $date, $time, $this->id)) {
            throw ValidationException::withMessages([
                'appointment_time' => 'This time slot is already booked.',
            ]);
        }

        try {
            $this->update([
                'doctor_id'        => $doctorId,
                'appointment_date' => Carbon::parse("$date $time"),
                'appointment_time' => $time,
            ]);
        } catch (QueryException $e) {
            throw static::translateSlotConflict($e);
        }
    }

    // ---------- Booking ----------
    public static function isAvailable($doctorId, $date, $time, $ignoreId = null)
    {
        return ! self::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_time', $time)
            ->where('status', '!=', self::CANCELLED)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    public static function book(array $data): self
    {
        try {
            return static::create($data);
        } catch (QueryException $e) {
            throw static::translateSlotConflict($e);
        }
    }

    public static function translateSlotConflict(QueryException $e): \Throwable
    {
        if (($e->errorInfo[1] ?? null) === 1062) {
            return ValidationException::withMessages([
                'appointment_time' => 'This time slot is already booked.',
            ]);
        }
        return $e;
    }

    public function getFullDateTimeAttribute()
    {
        if ($this->appointment_time) {
            return Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time);
        }
        return $this->appointment_date;
    }
}