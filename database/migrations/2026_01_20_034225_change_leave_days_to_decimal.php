<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeLeaveDaysToDecimal extends Migration
{
    public function up()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->decimal('leave_days', 3, 1)->change(); // max 9.9 days
        });
    }

    public function down()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->integer('leave_days')->change();
        });
    }
}
