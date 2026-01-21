<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlBalanceToInternsTable extends Migration
{
    public function up()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->integer('al_balance')->default(0)->after('end_date');
        });
    }

    public function down()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropColumn('al_balance');
        });
    }

}
