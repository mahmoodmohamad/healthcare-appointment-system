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

    public const SCHEDULED = 'scheduled';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public const TRANSITIONS = [
        self::SCHEDULED => [self::COMPLETED, self::CANCELLED],
        self::COMPLETED => [],
        self::CANCELLED => [],
    ];

    protected $fillable = [
        'patient_id', 'doctor_id', 'receptionist_id',
        'appointment_date', 'appointment_time', 'status', 'notes',
    ];

    protected $attributes = [
        'status' => self::SCHEDULED,
    ];

    protected $casts = ['appointment_date' => 'datetime'];

    /**
     * حماية على مستوى الـ Model: أي تغيير في status لازم يمشي
     * حسب TRANSITIONS (مثلاً completed → scheduled ممنوع)،
     * حتى لو جه من $appointment->update() أو من الـ API.
     */
    protected static function booted(): void
    {
        static::updating(function (self $appointment) {
            if (! $appointment->isDirty('status')) {
                return;
            }

            $from = (string) $appointment->getOriginal('status');
            $to   = (string) $appointment->status;

            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw InvalidAppointmentTransition::make($from, $to);
            }
        });
    }

    // ---------- Relationships ----------
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function receptionist()
    {
        return $this->belongsTo(Receptionist::class);
    }
    public function diagnosis()
    {
        return $this->hasOne(Diagnosis::class);
    }

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

    public function complete(): void
    {
        $this->transitionTo(self::COMPLETED);
    }
    public function cancel(): void
    {
        $this->transitionTo(self::CANCELLED);
    }

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
    public static function isAvailable($doctorId, $date, $time, $ignoreId = null): bool
    {
        // Normalize to HH:MM:SS so MySQL/PG/SQLite all behave the same
        $normalized = strlen($time) === 5 ? $time . ':00' : $time;

        return ! self::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_time', $normalized)
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

    public static function isDuplicateKey(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null;
        $code     = (int) ($e->errorInfo[1] ?? 0);

        return $code === 1062                                   // MySQL / MariaDB
            || $sqlState === '23505'                            // PostgreSQL
            || ($sqlState === '23000' && $code === 19           // SQLite
                && str_contains($e->getMessage(), 'UNIQUE'));
    }

    public static function translateSlotConflict(QueryException $e): \Throwable
    {
        if (static::isDuplicateKey($e)) {
            return ValidationException::withMessages([
                'appointment_time' => 'This time slot is already booked.',
            ]);
        }

        return $e;
    }

    // ---------- Accessors ----------
    public function getFullDateTimeAttribute()
    {
        if ($this->appointment_time) {
            return Carbon::parse($this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time);
        }

        return $this->appointment_date;
    }

    /** "10:00:00" → "10:00 AM" (للعرض فقط، من غير ما نغيّر appointment_time نفسه) */
    public function getFormattedTimeAttribute(): ?string
    {
        return $this->appointment_time
            ? Carbon::parse($this->appointment_time)->format('g:i A')
            : $this->appointment_date?->format('g:i A');
    }

    // ---------- Scopes ----------
    public function scopePendingFirst($query)
    {
        return $query
            ->withExists('diagnosis')
            ->orderByRaw("CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END")
            ->orderBy('diagnosis_exists')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time');
    }
}
