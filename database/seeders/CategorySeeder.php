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
                'en_name' => 'No Parent',
                'ar_name' => 'رئيسي',
                'en_description' => '',
                'ar_description' => '',
                'in_menu' => 0,
                'status' => 1,
            ],
        ];
        foreach ($roleadmins as $roleadmin) {
            Category::create($roleadmin);
        }
    }
}
