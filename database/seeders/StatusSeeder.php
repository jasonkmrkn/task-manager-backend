<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    // seed status table
    public function run(): void
    {
        Status::create(['name' => 'Not Completed']);
        Status::create(['name' => 'In Progress']);
        Status::create(['name' => 'Completed']);
    }
}
