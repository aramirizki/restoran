<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['cat_name' => 'makanan', 'description' => 'menu makanan'],
            ['cat_name' => 'minuman', 'description' => 'menu minuman'],

        ];

        
        DB::table('categories')->insert($categories);
        
    }
}
