<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Ensure HR account and example users are present
        $this->call(\Database\Seeders\HumanResourceSeeder::class);
        $this->call(\Database\Seeders\CreateHrCredentialsSeeder::class);
        // Create users for any employees that do not yet have one
        $this->call(\Database\Seeders\CreateUsersForEmployeesSeeder::class);
    }
}
