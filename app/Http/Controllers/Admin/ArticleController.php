<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Menampilkan daftar artikel yang sudah dipublikasikan dalam format grid/card
     * Digunakan untuk halaman feed artikel dengan fitur pencarian dan filter kategori
     */
    public function index(Request $request)
    {
        $query = Article::with(['user', 'category'])->where('status', 'published');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $articles = $query->latest()->paginate(9)->appends($request->only(['search', 'category']));
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    /**
     * Menampilkan halaman manajemen artikel dalam format tabel
     * Admin dapat melihat semua artikel (draft, published, archived) dengan fitur filter dan pencarian
     */
    public function manage(Request $request)
    {
        $query = Article::with(['user', 'category']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->latest()->paginate(10);
        return view('admin.articles.manage', compact('articles'));
    }

    /**
     * Menampilkan detail artikel beserta komentar-komentarnya
     * Admin dapat melihat artikel dengan status apapun (termasuk draft)
     * Otomatis menambah jumlah views artikel
     */
    public function show($slug)
    {
        $article = Article::with(['user', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('views');

        $comments = $article->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return view('admin.articles.show', compact('article', 'comments'));
    }

    /**
     * Menampilkan form untuk membuat artikel baru
     * Menyediakan daftar kategori aktif untuk dipilih
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.articles.create', compact('categories'));
    }

    /**
     * Menyimpan artikel baru ke database
     * Validasi input, generate slug unik, upload gambar, dan set published_at jika status published
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
        ]);

        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        Article::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->input('content'),
            'excerpt' => Str::limit(strip_tags($request->input('content')), 150),
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'image' => $imagePath,
            'status' => $request->status,
            'published_at' => $request->status == 'published' ? now() : null,
        ]);

        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit artikel
     * Menyediakan data artikel dan daftar kategori aktif
     */
    public function edit($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Memperbarui artikel yang sudah ada
     * Generate slug baru jika judul berubah, hapus gambar lama jika upload baru, update published_at jika dipublish
     */
    public function update(Request $request, $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($article->title !== $request->title) {
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
            $article->slug = $slug;
        }

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $article->image = $request->file('image')->store('articles', 'public');
        }

        $article->title = $request->title;
        $article->content = $request->input('content');
        $article->excerpt = Str::limit(strip_tags($request->input('content')), 150);
        $article->category_id = $request->category_id;
        $article->status = $request->status;

        if ($request->status == 'published' && !$article->published_at) {
            $article->published_at = now();
        }

        $article->save();

        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Menghapus artikel dari database
     * Menghapus gambar artikel dari storage jika ada
     */
    public function destroy($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }
        $article->delete();
        return redirect()->route('admin.articles')->with('success', 'Artikel berhasil dihapus.');
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

        $article = Article::where('slug', $slug)->firstOrFail();

        \App\Models\Comment::create([
            'content' => $request->input('content'),
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    /**
     * Menghapus komentar dari artikel
     * Komentar yang dihapus akan menghapus semua balasannya (cascade delete)
     */
    public function destroyComment($id)
    {
        $comment = \App\Models\Comment::findOrFail($id);
        $comment->delete();
        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Toggle like/unlike artikel oleh user
     * Jika sudah like maka unlike, jika belum like maka like
     * Update counter likes pada artikel
     */
    public function toggleLike($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
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
     * Toggle bookmark/unbookmark artikel oleh user
     * Jika sudah disimpan maka hapus dari bookmark, jika belum maka simpan ke bookmark
     */
    public function toggleBookmark($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
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
