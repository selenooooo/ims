<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToInternsTable extends Migration
{
   public function up()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed'])->default('active');
        });
    }

    public function down()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
