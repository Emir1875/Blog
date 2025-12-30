<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Menampilkan daftar artikel yang sudah dipublikasikan dalam format grid/card
     * Pelanggan hanya dapat melihat artikel yang sudah dipublikasikan
     * Mendukung fitur pencarian dan filter berdasarkan kategori
     */
    public function index(Request $request)
    {
        $articles = \App\Models\Article::with(['user', 'category'])
            ->where('status', 'published')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                        ->orWhere('excerpt', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->latest()
            ->paginate(9)
            ->appends($request->only(['search', 'category']));

        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('pelanggan.articles.index', compact('articles', 'categories'));
    }

    /**
     * Menampilkan detail artikel beserta komentar-komentarnya
     * Pelanggan hanya dapat melihat artikel yang sudah dipublikasikan
     * Otomatis menambah jumlah views artikel
     */
    public function show($slug)
    {
        $article = \App\Models\Article::with(['user', 'category'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $article->increment('views');

        $comments = $article->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return view('pelanggan.articles.show', compact('article', 'comments'));
    }

    /**
     * Menyimpan komentar baru pada artikel
     * Mendukung komentar utama dan balasan komentar (parent_id)
     */
    public function storeComment(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $article = \App\Models\Article::where('slug', $slug)->firstOrFail();

        \App\Models\Comment::create([
            'content' => $request->input('content'),
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Toggle like/unlike artikel oleh pelanggan
     * Jika sudah like maka unlike, jika belum like maka like
     * Update counter likes pada artikel
     */
    public function toggleLike($slug)
    {
        $article = \App\Models\Article::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if ($article->isLikedBy($user)) {
            $article->likedByUsers()->detach($user->id);
            $article->decrement('likes');
            $message = 'Unlike artikel berhasil.';
        } else {
            $article->likedByUsers()->attach($user->id);
            $article->increment('likes');
            $message = 'Like artikel berhasil.';
        }

        return back()->with('success', $message);
    }

    /**
     * Toggle bookmark/unbookmark artikel oleh pelanggan
     * Jika sudah disimpan maka hapus dari bookmark, jika belum maka simpan ke bookmark
     */
    public function toggleBookmark($slug)
    {
        $article = \App\Models\Article::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        if ($article->isBookmarkedBy($user)) {
            $article->bookmarkedByUsers()->detach($user->id);
            $message = 'Simpan artikel dibatalkan.';
        } else {
            $article->bookmarkedByUsers()->attach($user->id);
            $message = 'Artikel berhasil disimpan.';
        }

        return back()->with('success', $message);
    }
}
