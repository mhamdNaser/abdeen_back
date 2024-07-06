<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleadmins = [
            [
                'first_name' => 'Muhammed',
                'medium_name' => 'Khaled',
                'last_name' => 'Nasser Edden',
                'username' => 'Nasser',
                'email' => 'naser@atomi.com',
                'password' => Hash::make('Naser?2023'),
                'phone' => '0777777777',
                'role_id' => '1',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Muhammed',
                'medium_name' => 'ahmad',
                'last_name' => 'Nasser',
                'username' => 'Nasser11',
                'email' => 'naser1@atomi.com',
                'password' => Hash::make('123456'),
                'phone' => '0777777771',
                'role_id' => '2',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'ali',
                'medium_name' => 'ahmad',
                'last_name' => 'samer',
                'username' => 'ali22',
                'email' => 'ali22@atomi.com',
                'password' => Hash::make('123456'),
                'phone' => '0777777772',
                'role_id' => '2',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($roleadmins as $roleadmin) {
            Admin::create($roleadmin);
        }
    }
}
