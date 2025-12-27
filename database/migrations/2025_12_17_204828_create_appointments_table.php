<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
    $table->foreignId('patient_id')->constrained()->onDelete('cascade');
    $table->foreignId('physician_id')->constrained()->onDelete('cascade');
    $table->foreignId('secretary_id')->nullable()->constrained()->onDelete('set null');
    $table->dateTime('appointment_date');
	$table->time('appointment_time');
    $table->enum('status', [
        'scheduled',
        'completed',
        'cancelled'
    ])->default('scheduled');
    $table->text('notes')->nullable();
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
