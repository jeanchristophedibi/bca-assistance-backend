<?php

namespace Database\Seeders;

use App\Models\EntityEmail;
use Illuminate\Database\Seeder;

class EntityEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        EntityEmail::factory(10)->create();
    }
}
