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
        User::factory()->create([
            'name' => 'Aleks',
            'email' => 'alikmanukian@gmail.com',
            'password' => 'alikmanukian@gmail.com'
        ]);

        User::factory(1000)->create();
    }
}
