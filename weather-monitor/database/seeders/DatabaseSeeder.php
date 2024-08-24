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
		$this->call(RolesSeeder::class);

		if (!User::where('email', 'test@mail.com')->exists()) {
			User::factory()->create([
				'name' => 'Test User',
				'email' => 'test@mail.com',
				'password' => 'password',
				'email_verified_at' => now(),
			]);
		}

		if (!User::where('email', 'admin@mail.com')->exists()) {
			$adminRole = Role::findByName('admin');

			$admin = User::factory()->create([
				'name' => 'Admin User',
				'email' => 'admin@mail.com',
				'password' => 'password',
				'email_verified_at' => now(),
			]);

			$admin->assignRole($adminRole);
		}
    }
}
