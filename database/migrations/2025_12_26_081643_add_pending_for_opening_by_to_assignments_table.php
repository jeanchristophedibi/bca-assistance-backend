<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'opened_by')) {
                $table->unsignedBigInteger('opened_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'opened_at')) {
                $table->timestamp('opened_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'first_correction_validated')) {
                $table->boolean('first_correction_validated')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'first_correction_validated_by')) {
                $table->unsignedBigInteger('first_correction_validated_by')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'first_correction_validated_at')) {
                $table->timestamp('first_correction_validated_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'first_correction_unvalidated_by')) {
                $table->unsignedBigInteger('first_correction_unvalidated_by')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'first_correction_unvalidated_at')) {
                $table->timestamp('first_correction_unvalidated_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'first_correction_unvalidation_reason')) {
                $table->string('first_correction_unvalidation_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'second_correction_validated')) {
                $table->boolean('second_correction_validated')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'second_correction_validated_by')) {
                $table->unsignedBigInteger('second_correction_validated_by')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'second_correction_validated_at')) {
                $table->timestamp('second_correction_validated_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'second_correction_unvalidated_by')) {
                $table->unsignedBigInteger('second_correction_unvalidated_by')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'second_correction_unvalidated_at')) {
                $table->timestamp('second_correction_unvalidated_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'second_correction_unvalidation_reason')) {
                $table->string('second_correction_unvalidation_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_validated')) {
                $table->boolean('quote_validated')->default(false);
            }
            if (!Schema::hasColumn('assignments', 'quote_validated_by_repairer')) {
                $table->boolean('quote_validated_by_repairer')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_validated_by_repairer_by')) {
                $table->unsignedBigInteger('quote_validated_by_repairer_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_validated_by_repairer_at')) {
                $table->timestamp('quote_validated_by_repairer_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_validated_by_expert')) {
                $table->boolean('quote_validated_by_expert')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_validated_by_expert_by')) {
                $table->unsignedBigInteger('quote_validated_by_expert_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_validated_by_expert_at')) {
                $table->timestamp('quote_validated_by_expert_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'agreement_for_work_subject_to_conditions')) {
                $table->boolean('agreement_for_work_subject_to_conditions')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'agreement_for_work_subject_to_conditions_by')) {
                $table->unsignedBigInteger('agreement_for_work_subject_to_conditions_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'agreement_for_work_subject_to_conditions_at')) {
                $table->timestamp('agreement_for_work_subject_to_conditions_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'conditions')) {
                $table->text('conditions')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'classified_without_further_action_by')) {
                $table->unsignedBigInteger('classified_without_further_action_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'classified_without_further_action_at')) {
                $table->timestamp('classified_without_further_action_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_unvalidated_by_repairer_by')) {
                $table->unsignedBigInteger('quote_unvalidated_by_repairer_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_unvalidated_by_repairer_at')) {
                $table->timestamp('quote_unvalidated_by_repairer_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_unvalidation_by_repairer_reason')) {
                $table->string('quote_unvalidation_by_repairer_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_unvalidated_by_expert_by')) {
                $table->unsignedBigInteger('quote_unvalidated_by_expert_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_unvalidated_by_expert_at')) {
                $table->timestamp('quote_unvalidated_by_expert_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'quote_unvalidation_by_expert_reason')) {
                $table->string('quote_unvalidation_by_expert_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'is_validated_by_repairer')) {
                $table->boolean('is_validated_by_repairer')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'validated_by_repairer_by')) {
                $table->unsignedBigInteger('validated_by_repairer_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'validated_by_repairer_at')) {
                $table->timestamp('validated_by_repairer_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidated_by_repairer_by')) {
                $table->unsignedBigInteger('unvalidated_by_repairer_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidated_by_repairer_at')) {
                $table->timestamp('unvalidated_by_repairer_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidation_by_repairer_reason')) {
                $table->string('unvalidation_by_repairer_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'is_validated_by_expert')) {
                $table->boolean('is_validated_by_expert')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'validated_by_expert_by')) {
                $table->unsignedBigInteger('validated_by_expert_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'validated_by_expert_at')) {
                $table->timestamp('validated_by_expert_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidated_by_expert_by')) {
                $table->unsignedBigInteger('unvalidated_by_expert_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidated_by_expert_at')) {
                $table->timestamp('unvalidated_by_expert_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidation_by_expert_reason')) {
                $table->string('unvalidation_by_expert_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'edition_validated')) {
                $table->boolean('edition_validated')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'edition_validated_by')) {
                $table->unsignedBigInteger('edition_validated_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'edition_validated_at')) {
                $table->timestamp('edition_validated_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'edition_unvalidated_by')) {
                $table->unsignedBigInteger('edition_unvalidated_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'edition_unvalidated_at')) {
                $table->timestamp('edition_unvalidated_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'edition_unvalidation_reason')) {
                $table->string('edition_unvalidation_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidated_by')) {
                $table->unsignedBigInteger('unvalidated_by')->index()->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidated_at')) {
                $table->timestamp('unvalidated_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'unvalidation_reason')) {
                $table->text('unvalidation_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'deletion_reason')) {
                $table->text('deletion_reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'required_for_repairer_quote_validation')) {
                $table->boolean('required_for_repairer_quote_validation')->default(false)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'required_for_repairer_validation')) {
                $table->boolean('required_for_repairer_validation')->default(false)->nullable();
            }
        });

        // Add foreign key constraints if they don't exist
        $foreignKeyColumns = [
            'first_correction_validated_by',
            'second_correction_validated_by',
            'quote_validated_by_repairer_by',
            'quote_validated_by_expert_by',
            'quote_unvalidated_by_repairer_by',
            'quote_unvalidated_by_expert_by',
            'edition_validated_by',
            'edition_unvalidated_by',
            'validated_by_repairer_by',
            'unvalidated_by_repairer_by',
            'validated_by_expert_by',
            'unvalidated_by_expert_by',
            'unvalidated_by',
            'classified_without_further_action_by',
        ];

        foreach ($foreignKeyColumns as $column) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'assignments' 
                AND COLUMN_NAME = ? 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$column]);

            if (empty($foreignKeys)) {
                Schema::table('assignments', function (Blueprint $table) use ($column) {
                    $table->foreign($column)
                        ->references('id')
                        ->on('users')
                        ->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Drop foreign keys first if they exist
            $foreignKeyColumns = [
                'first_correction_validated_by',
                'second_correction_validated_by',
                'quote_validated_by_repairer_by',
                'quote_validated_by_expert_by',
                'quote_unvalidated_by_repairer_by',
                'quote_unvalidated_by_expert_by',
                'edition_validated_by',
                'edition_unvalidated_by',
                'validated_by_repairer_by',
                'unvalidated_by_repairer_by',
                'validated_by_expert_by',
                'unvalidated_by_expert_by',
                'unvalidated_by',
                'classified_without_further_action_by',
            ];

            foreach ($foreignKeyColumns as $column) {
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'assignments' 
                    AND COLUMN_NAME = ? 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ", [$column]);

                if (!empty($foreignKeys)) {
                    $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                    $table->dropForeign($constraintName);
                }
            }

            // Drop columns if they exist
            if (Schema::hasColumn('assignments', 'opened_by')) {
                $table->dropColumn('opened_by');
            }
            if (Schema::hasColumn('assignments', 'opened_at')) {
                $table->dropColumn('opened_at');
            }
            if (Schema::hasColumn('assignments', 'first_correction_validated')) {
                $table->dropColumn('first_correction_validated');
            }
            if (Schema::hasColumn('assignments', 'first_correction_validated_by')) {
                $table->dropColumn('first_correction_validated_by');
            }
            if (Schema::hasColumn('assignments', 'first_correction_validated_at')) {
                $table->dropColumn('first_correction_validated_at');
            }
            if (Schema::hasColumn('assignments', 'first_correction_unvalidated_by')) {
                $table->dropColumn('first_correction_unvalidated_by');
            }
            if (Schema::hasColumn('assignments', 'first_correction_unvalidated_at')) {
                $table->dropColumn('first_correction_unvalidated_at');
            }
            if (Schema::hasColumn('assignments', 'first_correction_unvalidation_reason')) {
                $table->dropColumn('first_correction_unvalidation_reason');
            }
            if (Schema::hasColumn('assignments', 'second_correction_validated')) {
                $table->dropColumn('second_correction_validated');
            }
            if (Schema::hasColumn('assignments', 'second_correction_validated_by')) {
                $table->dropColumn('second_correction_validated_by');
            }
            if (Schema::hasColumn('assignments', 'second_correction_validated_at')) {
                $table->dropColumn('second_correction_validated_at');
            }
            if (Schema::hasColumn('assignments', 'second_correction_unvalidated_by')) {
                $table->dropColumn('second_correction_unvalidated_by');
            }
            if (Schema::hasColumn('assignments', 'second_correction_unvalidated_at')) {
                $table->dropColumn('second_correction_unvalidated_at');
            }
            if (Schema::hasColumn('assignments', 'second_correction_unvalidation_reason')) {
                $table->dropColumn('second_correction_unvalidation_reason');
            }
            if (Schema::hasColumn('assignments', 'quote_validated')) {
                $table->dropColumn('quote_validated');
            }
            if (Schema::hasColumn('assignments', 'quote_validated_by_repairer')) {
                $table->dropColumn('quote_validated_by_repairer');
            }
            if (Schema::hasColumn('assignments', 'quote_validated_by_repairer_by')) {
                $table->dropColumn('quote_validated_by_repairer_by');
            }
            if (Schema::hasColumn('assignments', 'quote_validated_by_repairer_at')) {
                $table->dropColumn('quote_validated_by_repairer_at');
            }
            if (Schema::hasColumn('assignments', 'quote_validated_by_expert')) {
                $table->dropColumn('quote_validated_by_expert');
            }
            if (Schema::hasColumn('assignments', 'quote_validated_by_expert_by')) {
                $table->dropColumn('quote_validated_by_expert_by');
            }
            if (Schema::hasColumn('assignments', 'quote_validated_by_expert_at')) {
                $table->dropColumn('quote_validated_by_expert_at');
            }
            if (Schema::hasColumn('assignments', 'agreement_for_work_subject_to_conditions')) {
                $table->dropColumn('agreement_for_work_subject_to_conditions');
            }
            if (Schema::hasColumn('assignments', 'agreement_for_work_subject_to_conditions_by')) {
                $table->dropColumn('agreement_for_work_subject_to_conditions_by');
            }
            if (Schema::hasColumn('assignments', 'agreement_for_work_subject_to_conditions_at')) {
                $table->dropColumn('agreement_for_work_subject_to_conditions_at');
            }
            if (Schema::hasColumn('assignments', 'conditions')) {
                $table->dropColumn('conditions');
            }
            if (Schema::hasColumn('assignments', 'classified_without_further_action_by')) {
                $table->dropColumn('classified_without_further_action_by');
            }
            if (Schema::hasColumn('assignments', 'classified_without_further_action_at')) {
                $table->dropColumn('classified_without_further_action_at');
            }
            if (Schema::hasColumn('assignments', 'quote_unvalidated_by_repairer_by')) {
                $table->dropColumn('quote_unvalidated_by_repairer_by');
            }
            if (Schema::hasColumn('assignments', 'quote_unvalidated_by_repairer_at')) {
                $table->dropColumn('quote_unvalidated_by_repairer_at');
            }
            if (Schema::hasColumn('assignments', 'quote_unvalidation_by_repairer_reason')) {
                $table->dropColumn('quote_unvalidation_by_repairer_reason');
            }
            if (Schema::hasColumn('assignments', 'quote_unvalidated_by_expert_by')) {
                $table->dropColumn('quote_unvalidated_by_expert_by');
            }
            if (Schema::hasColumn('assignments', 'quote_unvalidated_by_expert_at')) {
                $table->dropColumn('quote_unvalidated_by_expert_at');
            }
            if (Schema::hasColumn('assignments', 'quote_unvalidation_by_expert_reason')) {
                $table->dropColumn('quote_unvalidation_by_expert_reason');
            }
            if (Schema::hasColumn('assignments', 'is_validated_by_repairer')) {
                $table->dropColumn('is_validated_by_repairer');
            }
            if (Schema::hasColumn('assignments', 'validated_by_repairer_by')) {
                $table->dropColumn('validated_by_repairer_by');
            }
            if (Schema::hasColumn('assignments', 'validated_by_repairer_at')) {
                $table->dropColumn('validated_by_repairer_at');
            }
            if (Schema::hasColumn('assignments', 'unvalidated_by_repairer_by')) {
                $table->dropColumn('unvalidated_by_repairer_by');
            }
            if (Schema::hasColumn('assignments', 'unvalidated_by_repairer_at')) {
                $table->dropColumn('unvalidated_by_repairer_at');
            }
            if (Schema::hasColumn('assignments', 'unvalidation_by_repairer_reason')) {
                $table->dropColumn('unvalidation_by_repairer_reason');
            }
            if (Schema::hasColumn('assignments', 'is_validated_by_expert')) {
                $table->dropColumn('is_validated_by_expert');
            }
            if (Schema::hasColumn('assignments', 'validated_by_expert_by')) {
                $table->dropColumn('validated_by_expert_by');
            }
            if (Schema::hasColumn('assignments', 'validated_by_expert_at')) {
                $table->dropColumn('validated_by_expert_at');
            }
            if (Schema::hasColumn('assignments', 'unvalidated_by_expert_by')) {
                $table->dropColumn('unvalidated_by_expert_by');
            }
            if (Schema::hasColumn('assignments', 'unvalidated_by_expert_at')) {
                $table->dropColumn('unvalidated_by_expert_at');
            }
            if (Schema::hasColumn('assignments', 'unvalidation_by_expert_reason')) {
                $table->dropColumn('unvalidation_by_expert_reason');
            }
            if (Schema::hasColumn('assignments', 'edition_validated')) {
                $table->dropColumn('edition_validated');
            }
            if (Schema::hasColumn('assignments', 'edition_validated_by')) {
                $table->dropColumn('edition_validated_by');
            }
            if (Schema::hasColumn('assignments', 'edition_validated_at')) {
                $table->dropColumn('edition_validated_at');
            }
            if (Schema::hasColumn('assignments', 'edition_unvalidated_by')) {
                $table->dropColumn('edition_unvalidated_by');
            }
            if (Schema::hasColumn('assignments', 'edition_unvalidated_at')) {
                $table->dropColumn('edition_unvalidated_at');
            }
            if (Schema::hasColumn('assignments', 'edition_unvalidation_reason')) {
                $table->dropColumn('edition_unvalidation_reason');
            }
            if (Schema::hasColumn('assignments', 'unvalidated_by')) {
                $table->dropColumn('unvalidated_by');
            }
            if (Schema::hasColumn('assignments', 'unvalidated_at')) {
                $table->dropColumn('unvalidated_at');
            }
            if (Schema::hasColumn('assignments', 'unvalidation_reason')) {
                $table->dropColumn('unvalidation_reason');
            }
            if (Schema::hasColumn('assignments', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
            if (Schema::hasColumn('assignments', 'deletion_reason')) {
                $table->dropColumn('deletion_reason');
            }
            if (Schema::hasColumn('assignments', 'required_for_repairer_quote_validation')) {
                $table->dropColumn('required_for_repairer_quote_validation');
            }
            if (Schema::hasColumn('assignments', 'required_for_repairer_validation')) {
                $table->dropColumn('required_for_repairer_validation');
            }
        });
    }
};
