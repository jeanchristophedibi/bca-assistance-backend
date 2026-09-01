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
            if (Schema::hasColumn('assignment_requests', 'insurer_id')) {
                $table->dropForeign(['insurer_id']);
            }

            if (Schema::hasColumn('assignment_requests', 'assignment_type_id')) {
                $table->dropForeign(['assignment_type_id']);
            }

            if (Schema::hasColumn('assignment_requests', 'expertise_type_id')) {
                $table->dropForeign(['expertise_type_id']);
            }

            $columns = collect([
                'policy_number',
                'claim_number',
                'insurer_id',
                'assignment_type_id',
                'expertise_type_id',
            ])->filter(fn (string $column) => Schema::hasColumn('assignment_requests', $column))
                ->values()
                ->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('assignment_requests', 'policy_number')) {
                $table->string('policy_number')->nullable();
            }

            if (! Schema::hasColumn('assignment_requests', 'claim_number')) {
                $table->string('claim_number')->nullable();
            }

            if (! Schema::hasColumn('assignment_requests', 'insurer_id')) {
                $table->unsignedBigInteger('insurer_id')->index()->nullable();
                $table->foreign('insurer_id')
                    ->references('id')
                    ->on('entities')
                    ->onDelete('cascade');
            }

            if (! Schema::hasColumn('assignment_requests', 'assignment_type_id')) {
                $table->unsignedBigInteger('assignment_type_id')->index()->nullable();
                $table->foreign('assignment_type_id')
                    ->references('id')
                    ->on('assignment_types')
                    ->onDelete('cascade');
            }

            if (! Schema::hasColumn('assignment_requests', 'expertise_type_id')) {
                $table->unsignedBigInteger('expertise_type_id')->index()->nullable();
                $table->foreign('expertise_type_id')
                    ->references('id')
                    ->on('expertise_types')
                    ->onDelete('cascade');
            }
        });
    }
};
