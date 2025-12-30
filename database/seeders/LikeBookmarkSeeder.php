<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Article;
use Illuminate\Support\Facades\DB;

class LikeBookmarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pelanggan
        $pelanggan = User::where('role', 'pelanggan')->first();

        if (!$pelanggan) {
            $this->command->warn('No pelanggan user found. Skipping...');
            return;
        }

        // Ambil beberapa artikel published
        $articles = Article::where('status', 'published')->take(5)->get();

        if ($articles->isEmpty()) {
            $this->command->warn('No published articles found. Skipping...');
            return;
        }

        // Clear existing data untuk user ini
        DB::table('article_likes')->where('user_id', $pelanggan->id)->delete();
        DB::table('bookmarks')->where('user_id', $pelanggan->id)->delete();

        // Tambahkan likes dengan timestamp berbeda
        foreach ($articles->take(3) as $index => $article) {
            DB::table('article_likes')->insert([
                'user_id' => $pelanggan->id,
                'article_id' => $article->id,
                'created_at' => now()->subDays($index),
                'updated_at' => now()->subDays($index),
            ]);

            $this->command->info("Liked: {$article->title}");
        }

        // Tambahkan bookmarks dengan timestamp berbeda
        foreach ($articles->take(4) as $index => $article) {
            DB::table('bookmarks')->insert([
                'user_id' => $pelanggan->id,
                'article_id' => $article->id,
                'created_at' => now()->subDays($index * 2),
                'updated_at' => now()->subDays($index * 2),
            ]);

            $this->command->info("Bookmarked: {$article->title}");
        }

        $this->command->info('Like and Bookmark seeder completed successfully!');
    }
}
