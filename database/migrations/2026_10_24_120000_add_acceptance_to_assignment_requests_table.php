<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Acceptation d'une demande : le dossier créé à partir d'elle, qui l'a acceptée et quand.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('assignment_id')->index()->nullable()->after('vehicle_id');
            $table->unsignedBigInteger('accepted_by')->index()->nullable()->after('created_at');
            $table->timestamp('accepted_at')->nullable()->after('accepted_by');

            $table->foreign('assignment_id')->references('id')->on('assignments')->nullOnDelete();
            $table->foreign('accepted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assignment_requests', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropForeign(['accepted_by']);
            $table->dropColumn(['assignment_id', 'accepted_by', 'accepted_at']);
        });
    }
};
