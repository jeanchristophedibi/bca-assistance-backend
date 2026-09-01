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
        Schema::table('shocks', function (Blueprint $table) {
            if (!Schema::hasColumn('shocks', 'is_for_work_sheet')) {
                $table->boolean('is_for_work_sheet')->default(false);
            }
            if (!Schema::hasColumn('shocks', 'quote_validated')) {
                $table->boolean('quote_validated')->default(false);
            }
            if (!Schema::hasColumn('shocks', 'is_before_quote')) {
                $table->boolean('is_before_quote')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shocks', function (Blueprint $table) {
            if (Schema::hasColumn('shocks', 'is_for_work_sheet')) {
                $table->dropColumn('is_for_work_sheet');
            }
            if (Schema::hasColumn('shocks', 'quote_validated')) {
                $table->dropColumn('quote_validated');
            }
            if (Schema::hasColumn('shocks', 'is_before_quote')) {
                $table->dropColumn('is_before_quote');
            }
        });
    }
};
