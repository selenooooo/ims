<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagsToLeaveTypesTable extends Migration
{
    public function up()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('affects_al_balance')
                  ->default(false)
                  ->after('name');

            $table->boolean('intern_allowed_apply')
                  ->default(true)
                  ->after('affects_al_balance');
        });
    }

    public function down()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn(['affects_al_balance', 'intern_allowed_apply']);
        });
    }
}
