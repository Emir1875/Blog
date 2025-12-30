<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard admin dengan statistik sistem
     * Menampilkan total users, articles, comments, dan artikel berdasarkan status
     * Menampilkan 5 artikel dan user terbaru
     */
    public function index()
    {
        $totalUsers = \App\Models\User::count();
        $totalArticles = \App\Models\Article::count();
        $totalComments = \App\Models\Comment::count();

        $totalPublished = \App\Models\Article::where('status', 'published')->count();
        $totalDrafts = \App\Models\Article::where('status', 'draft')->count();

        $recentArticles = \App\Models\Article::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = \App\Models\User::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalArticles',
            'totalComments',
            'totalPublished',
            'totalDrafts',
            'recentArticles',
            'recentUsers'
        ));
    }
}
