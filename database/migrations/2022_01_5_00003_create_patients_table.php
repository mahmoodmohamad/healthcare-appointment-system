<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('national_id')->unique();
    $table->string('phone');
	$table->enum('gender', ['male', 'female'])->nullable();
    $table->date('birth_date')->nullable();
    $table->foreignId('city_id')->constrained()->onDelete('cascade');
    $table->foreignId('secretary_id')->nullable()->constrained()->onDelete('set null');
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
        Schema::dropIfExists('patients');
    }
}
