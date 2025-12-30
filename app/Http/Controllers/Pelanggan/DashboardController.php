<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pelanggan dengan artikel published dan statistik personal
     * Menampilkan artikel yang disukai, disimpan, dan dibaca oleh pelanggan
     * Menampilkan 5 artikel paling populer (berdasarkan likes dan views)
     */
    public function index()
    {
        $user = auth()->user();
        $articles = \App\Models\Article::with(['user', 'category'])->where('status', 'published')->latest()->paginate(10);
        $jumlahKomentar = \App\Models\Comment::where('user_id', $user->id)->count();

        $artikelDisukai = \App\Models\Article::where('status', 'published')->orderBy('likes', 'desc')->take(5)->get();
        $artikelDisimpan = $user->bookmarkedArticles;
        $artikelDibaca = \App\Models\Article::where('status', 'published')->orderBy('views', 'desc')->take(5)->get();

        // Pagination untuk artikel yang disukai dan disimpan dengan pivot timestamps
        $myLikedArticles = $user->likedArticles()
            ->orderBy('article_likes.created_at', 'desc')
            ->paginate(5, ['*'], 'liked_page');

        $myBookmarkedArticles = $user->bookmarkedArticles()
            ->orderBy('bookmarks.created_at', 'desc')
            ->paginate(5, ['*'], 'bookmarked_page');

        $comments = \App\Models\Comment::with(['article', 'user'])->where('user_id', $user->id)->latest()->get();

        return view('pelanggan.dashboard.index', compact('articles', 'jumlahKomentar', 'artikelDisukai', 'artikelDisimpan', 'artikelDibaca', 'myBookmarkedArticles', 'myLikedArticles', 'comments'));
    }
}
