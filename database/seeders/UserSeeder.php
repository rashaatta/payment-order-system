<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
        ]);
        $admin->assignRole('admin');
        $admin->assignRole('customer');

        $customer = User::factory()->create([
            'name' => 'Rasha Atta',
            'email' => 'rashaatta83@gmail.com',
        ]);
        $customer->assignRole('customer');
    }
}
