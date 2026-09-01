<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            if (!Schema::hasColumn('photos', 'made_as_cover_at')) {
                $table->timestamp('made_as_cover_at')->nullable();
            }
            if (!Schema::hasColumn('photos', 'assignment_request_id')) {
                $table->unsignedBigInteger('assignment_request_id')->index()->nullable();
            }
        });

        // Add foreign key constraint if it doesn't exist
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'photos' 
            AND COLUMN_NAME = 'assignment_request_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (empty($foreignKeys)) {
            Schema::table('photos', function (Blueprint $table) {
                $table->foreign('assignment_request_id')
                    ->references('id')
                    ->on('assignment_requests')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'photos' 
                AND COLUMN_NAME = 'assignment_request_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                $table->dropForeign($constraintName);
            }
            // Drop columns if they exist
            if (Schema::hasColumn('photos', 'made_as_cover_at')) {
                $table->dropColumn('made_as_cover_at');
            }
            if (Schema::hasColumn('photos', 'assignment_request_id')) {
                $table->dropColumn('assignment_request_id');
            }
        });
    }
};
