<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveDaysToInternLeavesTable extends Migration
{
    public function up()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->decimal('leave_days', 2, 1)->after('half_day')->default(1.0);
        });

        // Optional: update existing records
        $leaves = \App\Models\InternLeave::all();
        foreach ($leaves as $leave) {
            $leave->leave_days = $leave->half_day === 'full' ? 1.0 : 0.5;
            $leave->save();
        }
    }

    public function down()
    {
        Schema::table('intern_leaves', function (Blueprint $table) {
            $table->dropColumn('leave_days');
        });
    }
}
