<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Common list filters + latest(created_at)
            $table->index(['status_id', 'created_at'], 'asg_status_created_at_idx');

            // AccessibleBy insurer users + latest(created_at)
            $table->index(['assignment_type_id', 'insurer_id', 'created_at'], 'asg_type_insurer_created_at_idx');

            // Common filters + latest(created_at)
            $table->index(['broker_id', 'created_at'], 'asg_broker_created_at_idx');
            $table->index(['repairer_id', 'created_at'], 'asg_repairer_created_at_idx');
            $table->index(['client_id', 'created_at'], 'asg_client_created_at_idx');

            // User-scoped filters + latest(created_at)
            $table->index(['opened_by', 'created_at'], 'asg_opened_by_created_at_idx');
            $table->index(['created_by', 'created_at'], 'asg_created_by_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex('asg_status_created_at_idx');
            $table->dropIndex('asg_type_insurer_created_at_idx');
            $table->dropIndex('asg_broker_created_at_idx');
            $table->dropIndex('asg_repairer_created_at_idx');
            $table->dropIndex('asg_client_created_at_idx');
            $table->dropIndex('asg_opened_by_created_at_idx');
            $table->dropIndex('asg_created_by_created_at_idx');
        });
    }
};

