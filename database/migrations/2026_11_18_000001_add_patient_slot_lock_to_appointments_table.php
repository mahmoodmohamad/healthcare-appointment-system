<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;
use RuntimeException;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('appointments')
            ->select('patient_id')
            ->selectRaw('DATE(appointment_date) as appointment_day')
            ->selectRaw('TIME(appointment_time) as appointment_clock')
            ->where('status', '!=', 'cancelled')
            ->groupBy('patient_id')
            ->groupByRaw('DATE(appointment_date)')
            ->groupByRaw('TIME(appointment_time)')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicates) {
            throw new RuntimeException(
                'Cannot add patient appointment slot protection: duplicate active patient time slots exist. Resolve these records before migrating.'
            );
        }

        $driver = DB::getDriverName();

        $expression = match ($driver) {
            'sqlite' => "CASE WHEN status = 'cancelled' THEN NULL ELSE CAST(patient_id AS TEXT) || '-' || date(appointment_date) || '-' || time(appointment_time) END",
            'pgsql' => "CASE WHEN status = 'cancelled' THEN NULL ELSE patient_id::text || '-' || to_char(appointment_date, 'YYYY-MM-DD') || '-' || to_char(appointment_time, 'HH24:MI:SS') END",
            default => "IF(status = 'cancelled', NULL, CONCAT(patient_id, '-', DATE(appointment_date), '-', appointment_time))",
        };

        Schema::table('appointments', function (Blueprint $table) use ($driver, $expression) {
            if ($driver === 'sqlite') {
                $table->string('patient_slot_lock', 80)->nullable()->virtualAs($expression);
            } else {
                $table->string('patient_slot_lock', 80)->nullable()->storedAs($expression);
            }

            $table->unique('patient_slot_lock');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique(['patient_slot_lock']);
            $table->dropColumn('patient_slot_lock');
        });
    }
};
