<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAlBalanceToDecimal extends Migration
{
    public function up()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->decimal('al_balance', 4, 1)->change();
        });
    }

    public function down()
    {
        Schema::table('interns', function (Blueprint $table) {
            $table->integer('al_balance')->change();
        });
    }
}
