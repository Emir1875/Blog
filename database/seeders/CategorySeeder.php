<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat kategori-kategori default untuk blog
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Technology', 'is_active' => true],
            ['name' => 'Lifestyle', 'is_active' => true],
            ['name' => 'Business', 'is_active' => true],
            ['name' => 'Health', 'is_active' => true],
            ['name' => 'Education', 'is_active' => true],
            ['name' => 'Entertainment', 'is_active' => true],
            ['name' => 'Travel', 'is_active' => true],
            ['name' => 'Food', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
