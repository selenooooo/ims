<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('intern_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('interns')->onDelete('cascade');
            $table->date('date'); // leave date
            $table->enum('type', ['AL', 'EL', 'MC', 'IOD']); // leave type
            $table->boolean('half_day')->default(false); // true = half day
            $table->enum('status', ['requested', 'approved', 'rejected'])->default('requested');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('intern_leaves');
    }
};
