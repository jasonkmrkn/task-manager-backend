<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    // seed priority table
    public function run(): void
    {
        Priority::create(['name' => 'Low']);
        Priority::create(['name' => 'Medium']);
        Priority::create(['name' => 'High']);
    }
}
