<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                "direction" => "LTR",
                "name"=> "English",
                "slug"=>"en",
                "default" => 1,
                "status"=>1
            ],
            [
                "direction" => "RTL",
                "name"=> "Arabic",
                "slug"=>"ar",
                "default" => 1,
                "status"=>1
            ]
        ];
        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
