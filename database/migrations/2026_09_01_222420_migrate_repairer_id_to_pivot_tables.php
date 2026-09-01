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
        if (Schema::hasColumn('assignment_requests', 'repairer_id')) {
            $rows = DB::table('assignment_requests')
                ->whereNotNull('repairer_id')
                ->select('id', 'repairer_id', 'created_by', 'updated_by', 'created_at', 'updated_at')
                ->get();

            foreach ($rows as $row) {
                DB::table('assignment_request_repairers')->insert([
                    'assignment_request_id' => $row->id,
                    'repairer_id' => $row->repairer_id,
                    'created_by' => $row->created_by,
                    'updated_by' => $row->updated_by,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            Schema::table('assignment_requests', function (Blueprint $table) {
                $table->dropForeign(['repairer_id']);
                $table->dropColumn('repairer_id');
            });
        }

        if (Schema::hasColumn('assignments', 'repairer_id')) {
            $rows = DB::table('assignments')
                ->whereNotNull('repairer_id')
                ->select('id', 'repairer_id', 'created_by', 'updated_by', 'created_at', 'updated_at')
                ->get();

            foreach ($rows as $row) {
                DB::table('assignment_repairers')->insert([
                    'assignment_id' => $row->id,
                    'repairer_id' => $row->repairer_id,
                    'created_by' => $row->created_by,
                    'updated_by' => $row->updated_by,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            if (Schema::hasIndex('assignments', 'asg_repairer_created_at_idx')) {
                Schema::table('assignments', function (Blueprint $table) {
                    $table->dropIndex('asg_repairer_created_at_idx');
                });
            }

            Schema::table('assignments', function (Blueprint $table) {
                $table->dropForeign(['repairer_id']);
                $table->dropColumn('repairer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('assignment_requests', 'repairer_id')) {
            Schema::table('assignment_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('repairer_id')->index()->nullable();
                $table->foreign('repairer_id')
                    ->references('id')
                    ->on('entities')
                    ->onDelete('cascade');
            });

            $rows = DB::table('assignment_request_repairers')
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->get()
                ->groupBy('assignment_request_id');

            foreach ($rows as $assignmentRequestId => $pivots) {
                DB::table('assignment_requests')
                    ->where('id', $assignmentRequestId)
                    ->update(['repairer_id' => $pivots->first()->repairer_id]);
            }
        }

        if (! Schema::hasColumn('assignments', 'repairer_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->unsignedBigInteger('repairer_id')->index()->nullable();
                $table->foreign('repairer_id')
                    ->references('id')
                    ->on('entities')
                    ->onDelete('cascade');
            });

            $rows = DB::table('assignment_repairers')
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->get()
                ->groupBy('assignment_id');

            foreach ($rows as $assignmentId => $pivots) {
                DB::table('assignments')
                    ->where('id', $assignmentId)
                    ->update(['repairer_id' => $pivots->first()->repairer_id]);
            }
        }
    }
};
