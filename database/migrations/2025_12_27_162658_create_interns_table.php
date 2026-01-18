<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('interns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // link to users table
            $table->string('employee_id')->unique();
            $table->date('report_date');
            $table->integer('intern_duration'); // in months
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interns');
    }
};
