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
        Schema::table('shock_works', function (Blueprint $table) {
            if (!Schema::hasColumn('shock_works', 'old_supply_id')) {
                $table->unsignedBigInteger('old_supply_id')->index()->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_disassembly')) {
                $table->boolean('old_disassembly')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'old_replacement')) {
                $table->boolean('old_replacement')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'old_repair')) {
                $table->boolean('old_repair')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'old_paint')) {
                $table->boolean('old_paint')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'old_obsolescence')) {
                $table->boolean('old_obsolescence')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'old_control')) {
                $table->boolean('old_control')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'old_in_order')) {
                $table->boolean('old_in_order')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'old_comment')) {
                $table->text('old_comment')->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_amount')) {
                $table->decimal('old_amount', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_obsolescence_rate')) {
                $table->decimal('old_obsolescence_rate', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_obsolescence_amount_excluding_tax')) {
                $table->decimal('old_obsolescence_amount_excluding_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_obsolescence_amount_tax')) {
                $table->decimal('old_obsolescence_amount_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_obsolescence_amount')) {
                $table->decimal('old_obsolescence_amount', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_recovery_amount_excluding_tax')) {
                $table->decimal('old_recovery_amount_excluding_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_recovery_amount_tax')) {
                $table->decimal('old_recovery_amount_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_recovery_amount')) {
                $table->decimal('old_recovery_amount', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_discount')) {
                $table->decimal('old_discount', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_discount_amount_excluding_tax')) {
                $table->decimal('old_discount_amount_excluding_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_discount_amount_tax')) {
                $table->decimal('old_discount_amount_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_discount_amount')) {
                $table->decimal('old_discount_amount', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_new_amount_excluding_tax')) {
                $table->decimal('old_new_amount_excluding_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_new_amount_tax')) {
                $table->decimal('old_new_amount_tax', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'old_new_amount')) {
                $table->decimal('old_new_amount', 18, 2)->nullable();
            }
            if (!Schema::hasColumn('shock_works', 'is_before_quote')) {
                $table->boolean('is_before_quote')->default(false);
            }
            if (!Schema::hasColumn('shock_works', 'quote_validated')) {
                $table->boolean('quote_validated')->default(false);
            }
        });

        // Add foreign key constraint if it doesn't exist
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'shock_works' 
            AND COLUMN_NAME = 'old_supply_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (empty($foreignKeys)) {
            Schema::table('shock_works', function (Blueprint $table) {
                $table->foreign('old_supply_id')
                    ->references('id')
                    ->on('supplies')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shock_works', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'shock_works' 
                AND COLUMN_NAME = 'old_supply_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            if (!empty($foreignKeys)) {
                $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
                $table->dropForeign($constraintName);
            }

            if (Schema::hasColumn('shock_works', 'old_supply_id')) {
                $table->dropColumn('old_supply_id');
            }
            if (Schema::hasColumn('shock_works', 'old_disassembly')) {
                $table->dropColumn('old_disassembly');
            }
            if (Schema::hasColumn('shock_works', 'old_replacement')) {
                $table->dropColumn('old_replacement');
            }
            if (Schema::hasColumn('shock_works', 'old_repair')) {
                $table->dropColumn('old_repair');
            }
            if (Schema::hasColumn('shock_works', 'old_paint')) {
                $table->dropColumn('old_paint');
            }
            if (Schema::hasColumn('shock_works', 'old_obsolescence')) {
                $table->dropColumn('old_obsolescence');
            }
            if (Schema::hasColumn('shock_works', 'old_control')) {
                $table->dropColumn('old_control');
            }
            if (Schema::hasColumn('shock_works', 'old_in_order')) {
                $table->dropColumn('old_in_order');
            }
            if (Schema::hasColumn('shock_works', 'old_comment')) {
                $table->dropColumn('old_comment');
            }
            if (Schema::hasColumn('shock_works', 'old_amount')) {
                $table->dropColumn('old_amount');
            }
            if (Schema::hasColumn('shock_works', 'old_obsolescence_rate')) {
                $table->dropColumn('old_obsolescence_rate');
            }
            if (Schema::hasColumn('shock_works', 'old_obsolescence_amount_excluding_tax')) {
                $table->dropColumn('old_obsolescence_amount_excluding_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_obsolescence_amount_tax')) {
                $table->dropColumn('old_obsolescence_amount_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_obsolescence_amount')) {
                $table->dropColumn('old_obsolescence_amount');
            }
            if (Schema::hasColumn('shock_works', 'old_recovery_amount_excluding_tax')) {
                $table->dropColumn('old_recovery_amount_excluding_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_recovery_amount_tax')) {
                $table->dropColumn('old_recovery_amount_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_recovery_amount')) {
                $table->dropColumn('old_recovery_amount');
            }
            if (Schema::hasColumn('shock_works', 'old_discount')) {
                $table->dropColumn('old_discount');
            }
            if (Schema::hasColumn('shock_works', 'old_discount_amount_excluding_tax')) {
                $table->dropColumn('old_discount_amount_excluding_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_discount_amount_tax')) {
                $table->dropColumn('old_discount_amount_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_discount_amount')) {
                $table->dropColumn('old_discount_amount');
            }
            if (Schema::hasColumn('shock_works', 'old_new_amount_excluding_tax')) {
                $table->dropColumn('old_new_amount_excluding_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_new_amount_tax')) {
                $table->dropColumn('old_new_amount_tax');
            }
            if (Schema::hasColumn('shock_works', 'old_new_amount')) {
                $table->dropColumn('old_new_amount');
            }
            if (Schema::hasColumn('shock_works', 'is_before_quote')) {
                $table->dropColumn('is_before_quote');
            }
            if (Schema::hasColumn('shock_works', 'quote_validated')) {
                $table->dropColumn('quote_validated');
            }
        });
    }
};
