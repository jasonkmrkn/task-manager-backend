<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    // seed db
    public function run(): void
    {
        $this->call([
            StatusSeeder::Class,
            PrioritySeeder::class,
        ]);
    }
}
