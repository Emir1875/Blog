<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\Article;
use App\Models\User;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Skip untuk production - komentar akan dibuat oleh user
     */
    public function run(): void
    {
        // Skip comment seeding untuk production
        // Komentar akan dibuat oleh user secara natural
        $this->command->info('Skipping CommentSeeder for production.');
    }
}
