<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $category = Category::where('is_active', true)->first();

        if ($admin && $category) {
            // Kita buat 20 artikel baru setiap kali seeder dijalankan
            for ($i = 1; $i <= 20; $i++) {
                // Kita tambahkan id unik/random agar slug tidak duplikat
                $title = 'Artikel Random Ke-' . Str::random(5); 
                
                Article::create([
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . rand(1000, 9999),
                    'content' => 'Ini adalah konten artikel otomatis yang berbeda-beda.',
                    'excerpt' => 'Ringkasan artikel otomatis.',
                    'category_id' => $category->id,
                    'user_id' => $admin->id,
                    'status' => 'published',
                    'published_at' => now(),
                    'views' => rand(0, 100),
                    'likes' => rand(0, 50),
                ]);
            }
            $this->command->info('20 Artikel baru berhasil ditambahkan tanpa bentrok!');
        }
    }
}