<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard staff dengan artikel published dan statistik personal
     * Menampilkan artikel yang disukai, disimpan, dan dibaca oleh staff
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

        $myLikedArticles = $user->likedArticles()->latest()->take(5)->get();
        $myBookmarkedArticles = $user->bookmarkedArticles()->latest()->take(5)->get();

        $comments = \App\Models\Comment::with(['article', 'user'])->where('user_id', $user->id)->latest()->get();

        return view('staff.dashboard.index', compact('articles', 'jumlahKomentar', 'artikelDisukai', 'artikelDisimpan', 'artikelDibaca', 'myBookmarkedArticles', 'myLikedArticles', 'comments'));
    }
}
