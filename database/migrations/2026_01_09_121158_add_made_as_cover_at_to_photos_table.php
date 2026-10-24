<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\ChecksForeignKeyExists;

return new class extends Migration
{
    use ChecksForeignKeyExists;

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
        $foreignKeys = $this->foreignKeyExists('photos', 'assignment_request_id');

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
            $foreignKeys = $this->foreignKeyExists('photos', 'assignment_request_id');

            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->constraint_name;
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
