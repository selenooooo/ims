<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToInternLeaves extends Migration
{
    public function up()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->unique(
                ['user_id', 'leave_date', 'half_day'],
                'intern_leaves_unique_user_date_halfday'
            );
        });
    }

    public function down()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->dropUnique('intern_leaves_unique_user_date_halfday');
        });
    }

}
