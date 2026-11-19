<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

return new class extends Migration
{
    public function up(): void
    {
        $hasColumn = Schema::hasColumn('appointments', 'patient_slot_lock');
        $indexes = Schema::getIndexes('appointments');
        $hasUniqueIndex = collect($indexes)->contains(
            fn (array $index): bool => ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['patient_slot_lock']
        );

        $driver = DB::getDriverName();

        $dayExpression = match ($driver) {
            'pgsql' => 'appointment_date::date',
            default => 'DATE(appointment_date)',
        };
        $timeExpression = match ($driver) {
            'pgsql' => 'appointment_time::time',
            default => 'TIME(appointment_time)',
        };

        $duplicates = ! $hasUniqueIndex && DB::table('appointments')
            ->select('patient_id')
            ->selectRaw("{$dayExpression} as appointment_day")
            ->selectRaw("{$timeExpression} as appointment_clock")
            ->where('status', '!=', 'cancelled')
            ->groupBy('patient_id')
            ->groupByRaw("{$dayExpression}, {$timeExpression}")
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicates) {
            throw new RuntimeException(
                'Cannot add patient appointment slot protection: duplicate active patient time slots exist. Resolve these records before migrating.'
            );
        }

        $expression = match ($driver) {
            'sqlite' => "CASE WHEN status = 'cancelled' THEN NULL ELSE CAST(patient_id AS TEXT) || '-' || date(appointment_date) || '-' || time(appointment_time) END",
            'pgsql' => "CASE WHEN status = 'cancelled' THEN NULL ELSE patient_id::text || '-' || appointment_date::date::text || '-' || appointment_time::text END",
            default => "IF(status = 'cancelled', NULL, CONCAT(patient_id, '-', DATE(appointment_date), '-', appointment_time))",
        };

        Schema::table('appointments', function (Blueprint $table) use (
            $driver,
            $expression,
            $hasColumn,
            $hasUniqueIndex
        ) {
            if (! $hasColumn) {
                if ($driver === 'sqlite') {
                    $table->string('patient_slot_lock', 80)->nullable()->virtualAs($expression);
                } else {
                    $table->string('patient_slot_lock', 80)->nullable()->storedAs($expression);
                }
            }

            if (! $hasUniqueIndex) {
                $table->unique('patient_slot_lock');
            }
        });
    }

    public function down(): void
    {
        /*
         * Some databases may already have this generated column from an
         * earlier version of the appointments table migration. We cannot
         * reliably distinguish that legacy column from one added here.
         * Keep it in place on rollback rather than deleting another
         * migration's schema or risking existing appointment constraints.
         */
    }
};
