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
        Schema::table('assignment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_requests', 'claim_date') && ! Schema::hasColumn('assignment_requests', 'date')) {
                $table->renameColumn('claim_date', 'date');
            }

            if (Schema::hasColumn('assignment_requests', 'expertise_place') && ! Schema::hasColumn('assignment_requests', 'assistance_place')) {
                $table->renameColumn('expertise_place', 'assistance_place');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_requests', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_requests', 'date') && ! Schema::hasColumn('assignment_requests', 'claim_date')) {
                $table->renameColumn('date', 'claim_date');
            }

            if (Schema::hasColumn('assignment_requests', 'assistance_place') && ! Schema::hasColumn('assignment_requests', 'expertise_place')) {
                $table->renameColumn('assistance_place', 'expertise_place');
            }
        });
    }
};
