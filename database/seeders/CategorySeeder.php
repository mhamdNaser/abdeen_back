<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleadmins = [
            [
                'name' => 'No Parent',
                'description' => '',
                'status' => 1,
            ],
        ];
        foreach ($roleadmins as $roleadmin) {
            Category::create($roleadmin);
        }
    }
}
