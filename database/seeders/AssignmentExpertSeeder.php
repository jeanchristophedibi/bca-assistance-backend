<?php

namespace Database\Seeders;

use App\Models\AssignmentExpert;
use Illuminate\Database\Seeder;

class AssignmentExpertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        AssignmentExpert::factory(10)->create();
    }
}
