<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();

            $table->decimal('total_hours', 5, 2)->nullable();

            $table->enum('status', ['present', 'late', 'absent'])
                  ->default('present');

            $table->timestamps();

            // Prevent duplicate attendance per day
            $table->unique(['user_id', 'attendance_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
