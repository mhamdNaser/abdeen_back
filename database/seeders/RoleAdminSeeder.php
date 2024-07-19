<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use Illuminate\Database\Seeder;
use App\Models\RoleAdmin;

class RoleAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'super admin',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5251,
                'name' => 'developer',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($admins as $admin) {
            AdminRole::create($admin);
        }
    }
}
