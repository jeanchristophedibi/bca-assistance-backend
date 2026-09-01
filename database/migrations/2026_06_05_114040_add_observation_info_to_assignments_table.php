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
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'observation_realizer_name')) {
                $table->string('observation_realizer_name')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'observation_realizer_phone')) {
                $table->string('observation_realizer_phone')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'observation_place')) {
                $table->string('observation_place')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'observation_date')) {
                $table->date('observation_date')->nullable();
            }
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'observation_realizer_name')) {
                $table->dropColumn('observation_realizer_name');
            }
            if (Schema::hasColumn('assignments', 'observation_realizer_phone')) {
                $table->dropColumn('observation_realizer_phone');
            }
            if (Schema::hasColumn('assignments', 'observation_place')) {
                $table->dropColumn('observation_place');
            }
            if (Schema::hasColumn('assignments', 'observation_date')) {
                $table->dropColumn('observation_date');
            }
        });
    }
};
