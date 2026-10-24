<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Motif de clôture : écrit par AssignmentController::close() et validé par
     * CloseAssignmentRequest, mais la colonne n'avait jamais été créée.
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (! Schema::hasColumn('assignments', 'closing_reason')) {
                $table->text('closing_reason')->nullable()->after('cancellation_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'closing_reason')) {
                $table->dropColumn('closing_reason');
            }
        });
    }
};
