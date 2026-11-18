<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = DB::getDriverName();

        $slotExpression = match ($driver) {
            'sqlite' => "CASE WHEN status = 'cancelled' THEN NULL ELSE CAST(doctor_id AS TEXT) || '-' || date(appointment_date) || '-' || time(appointment_time) END",
            'pgsql' => "CASE WHEN status = 'cancelled' THEN NULL ELSE doctor_id::text || '-' || to_char(appointment_date, 'YYYY-MM-DD') || '-' || to_char(appointment_time, 'HH24:MI:SS') END",
            default => "IF(status = 'cancelled', NULL, CONCAT(doctor_id, '-', DATE(appointment_date), '-', appointment_time))",
        };

        $patientSlotExpression = match ($driver) {
            'sqlite' => "CASE WHEN status = 'cancelled' THEN NULL ELSE CAST(patient_id AS TEXT) || '-' || date(appointment_date) || '-' || time(appointment_time) END",
            'pgsql' => "CASE WHEN status = 'cancelled' THEN NULL ELSE patient_id::text || '-' || to_char(appointment_date, 'YYYY-MM-DD') || '-' || to_char(appointment_time, 'HH24:MI:SS') END",
            default => "IF(status = 'cancelled', NULL, CONCAT(patient_id, '-', DATE(appointment_date), '-', appointment_time))",
        };

        Schema::create('appointments', function (Blueprint $table) use ($slotExpression, $patientSlotExpression) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('receptionist_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', [
                'scheduled',
                'completed',
                'cancelled'
            ])->default('scheduled');
            $table->text('notes')->nullable();
            $table->string('slot_lock', 80)->nullable()->unique()
                   ->storedAs($slotExpression);
            $table->string('patient_slot_lock', 80)->nullable()->unique()
                   ->storedAs($patientSlotExpression);
            $table->index(['doctor_id', 'appointment_date']);
            $table->index('patient_id');
            $table->index('receptionist_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
