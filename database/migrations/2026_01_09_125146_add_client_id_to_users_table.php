<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'client_id')) {
                $table->unsignedBigInteger('client_id')->index()->nullable();
            }
        });

        // Add foreign key constraint if it doesn't exist
        $foreignKeys = $this->foreignKeyExists('users', 'client_id');

        if (empty($foreignKeys)) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $foreignKeys = $this->foreignKeyExists('users', 'client_id');

            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->constraint_name;
                $table->dropForeign($constraintName);
            }
            // Drop columns if they exist
            if (Schema::hasColumn('users', 'client_id')) {
                $table->dropColumn('client_id');
            }
        });
    }
};
