<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('mode')->default('standard')->after('is_active');
            $table->integer('num_teams')->nullable()->after('mode');
            $table->string('status')->default('waiting')->after('num_teams'); // waiting, started, finished
            $table->timestamp('started_at')->nullable()->after('status');
        });

        Schema::table('student_sessions', function (Blueprint $table) {
            $table->string('team_name')->nullable()->after('student_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['mode', 'num_teams', 'status', 'started_at']);
        });

        Schema::table('student_sessions', function (Blueprint $table) {
            $table->dropColumn('team_name');
        });
    }
};
