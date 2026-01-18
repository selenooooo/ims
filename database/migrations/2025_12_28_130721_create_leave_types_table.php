<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveTypesTable extends Migration
{
    public function up()
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // AL, EL, MC, IOD
            $table->string('name');
            $table->timestamps();
        });

        DB::table('leave_types')->insert([
            ['code' => 'AL', 'name' => 'Annual Leave'],
            ['code' => 'EL', 'name' => 'Emergency Leave'],
            ['code' => 'MC', 'name' => 'Medical Leave'],
            ['code' => 'IOD', 'name' => 'Intern Off Day'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('leave_types');
    }
}
