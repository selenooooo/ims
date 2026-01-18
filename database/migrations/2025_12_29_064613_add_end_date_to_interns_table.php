<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndDateToInternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('report_date');
        });
    }

    public function down()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }

}
