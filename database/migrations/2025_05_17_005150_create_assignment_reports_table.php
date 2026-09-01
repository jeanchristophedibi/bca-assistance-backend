<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('assignment_reports', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('reference')->unique();
            $table->date('seen_before_work_date')->nullable();
            $table->date('seen_during_work_date')->nullable();
            $table->date('seen_after_work_date')->nullable();
            $table->date('contact_date')->nullable();
            $table->string('place_of_visit')->nullable();
            $table->boolean('assured_value')->default(false);
            $table->decimal('salvage_value', 18, 2)->nullable();
            $table->decimal('new_value', 18, 2)->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->decimal('market_value', 18, 2)->nullable();
            $table->string('work_duration')->nullable();
            $table->text('expert_remark')->nullable();
            $table->decimal('fee', 18, 2)->nullable();
            $table->decimal('work_fee', 18, 2)->nullable();
            $table->decimal('photo_fee', 18, 2)->nullable();
            $table->decimal('amount_excluding_tax', 18, 2)->nullable();
            $table->decimal('amount_tax', 18, 2)->nullable();
            $table->decimal('amount', 18, 2)->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->unsignedBigInteger('assignment_id')->index()->nullable();
            $table->unsignedBigInteger('technical_conclusion_id')->index()->nullable();
            $table->unsignedBigInteger('status_id')->index()->nullable();
            $table->unsignedBigInteger('created_by')->index()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('updated_by')->index()->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->index()->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('assignment_id')
                ->references('id')
                ->on('assignments')
                ->onDelete('cascade');

            $table->foreign('technical_conclusion_id')
                ->references('id')
                ->on('technical_conclusions')
                ->onDelete('cascade');

            $table->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_reports');
    }
};
