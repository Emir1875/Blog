<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 1 artikel contoh untuk testing
     */
    public function run(): void
    {
        // Skip jika tidak ada user atau kategori
        if (User::count() === 0 || Category::count() === 0) {
            $this->command->warn('Skipping ArticleSeeder: No users or categories found.');
            return;
        }

        // Buat 1 artikel contoh
        $admin = User::where('role', 'admin')->first();
        $category = Category::where('is_active', true)->first();

        if ($admin && $category) {
            Article::create([
                'title' => 'Welcome to Our Blog',
                'slug' => 'welcome-to-our-blog',
                'content' => 'This is a sample article. You can edit or delete this article from the admin panel.',
                'excerpt' => 'This is a sample article for testing purposes.',
                'category_id' => $category->id,
                'user_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'views' => 0,
                'likes' => 0,
            ]);
        }
    }
}
