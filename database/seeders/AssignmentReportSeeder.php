<?php

namespace Database\Seeders;

use App\Models\AssignmentReport;
use Illuminate\Database\Seeder;

class AssignmentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        AssignmentReport::factory(10)->create();
    }
}
