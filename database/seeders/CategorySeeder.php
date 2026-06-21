<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Makanan Kucing',
            'Makanan Anjing',
            'Vitamin',
        ];

        foreach ($categories as $categoryName) {
            \App\Models\Category::firstOrCreate(['name' => $categoryName]);
        }
    }
}
