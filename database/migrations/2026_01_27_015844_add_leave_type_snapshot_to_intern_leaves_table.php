<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveTypeSnapshotToInternLeavesTable extends Migration
{
    public function up()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->string('leave_type_snapshot')
                  ->nullable()
                  ->after('leave_type_id');
        });
    }

    public function down()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->dropColumn('leave_type_snapshot');
        });
    }
}
