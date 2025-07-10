<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@ro.ru',
            'password' => '23142314',
        ]);
        $user->assignRole('admin');
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
