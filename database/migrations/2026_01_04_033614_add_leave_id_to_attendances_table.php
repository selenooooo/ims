<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('leave_id')->nullable()->after('user_id');

            // optional but recommended
            $table->foreign('leave_id')
                ->references('id')
                ->on('intern_leaves')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['leave_id']);
            $table->dropColumn('leave_id');
        });
    }
};
