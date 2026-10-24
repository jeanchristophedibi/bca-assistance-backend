<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Support\ChecksForeignKeyExists;

return new class extends Migration
{
    use ChecksForeignKeyExists;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workforces', function (Blueprint $table) {
            if (!Schema::hasColumn('workforces', 'old_nb_hours')) {
                $table->decimal('old_nb_hours', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('workforces', 'old_work_fee')) {
                $table->decimal('old_work_fee', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('workforces', 'old_discount')) {
                $table->decimal('old_discount', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('workforces', 'old_amount_excluding_tax')) {
                $table->decimal('old_amount_excluding_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('workforces', 'old_amount_tax')) {
                $table->decimal('old_amount_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('workforces', 'old_amount')) {
                $table->decimal('old_amount', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('workforces', 'old_with_tax')) {
                $table->boolean('old_with_tax')->default(false);
            }
            if (!Schema::hasColumn('workforces', 'old_all_paint')) {
                $table->boolean('old_all_paint')->default(false);
            }
            if (!Schema::hasColumn('workforces', 'is_before_quote')) {
                $table->boolean('is_before_quote')->default(false);
            }
            if (!Schema::hasColumn('workforces', 'quote_validated')) {
                $table->boolean('quote_validated')->default(false);
            }
            if (!Schema::hasColumn('workforces', 'old_workforce_type_id')) {
                $table->unsignedBigInteger('old_workforce_type_id')->index()->nullable();
            }
        });

        // Add foreign key constraint if it doesn't exist
        $foreignKeys = $this->foreignKeyExists('workforces', 'old_workforce_type_id');

        if (empty($foreignKeys)) {
            Schema::table('workforces', function (Blueprint $table) {
                $table->foreign('old_workforce_type_id')
                    ->references('id')
                    ->on('workforce_types')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workforces', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $foreignKeys = $this->foreignKeyExists('workforces', 'old_workforce_type_id');

            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->constraint_name;
                $table->dropForeign($constraintName);
            }

            if (Schema::hasColumn('workforces', 'old_nb_hours')) {
                $table->dropColumn('old_nb_hours');
            }
            if (Schema::hasColumn('workforces', 'old_work_fee')) {
                $table->dropColumn('old_work_fee');
            }
            if (Schema::hasColumn('workforces', 'old_discount')) {
                $table->dropColumn('old_discount');
            }
            if (Schema::hasColumn('workforces', 'old_amount_excluding_tax')) {
                $table->dropColumn('old_amount_excluding_tax');
            }
            if (Schema::hasColumn('workforces', 'old_amount_tax')) {
                $table->dropColumn('old_amount_tax');
            }
            if (Schema::hasColumn('workforces', 'old_amount')) {
                $table->dropColumn('old_amount');
            }
            if (Schema::hasColumn('workforces', 'old_with_tax')) {
                $table->dropColumn('old_with_tax');
            }
            if (Schema::hasColumn('workforces', 'old_all_paint')) {
                $table->dropColumn('old_all_paint');
            }
            if (Schema::hasColumn('workforces', 'is_before_quote')) {
                $table->dropColumn('is_before_quote');
            }
            if (Schema::hasColumn('workforces', 'quote_validated')) {
                $table->dropColumn('quote_validated');
            }
            if (Schema::hasColumn('workforces', 'old_workforce_type_id')) {
                $table->dropColumn('old_workforce_type_id');
            }
        });
    }
};
