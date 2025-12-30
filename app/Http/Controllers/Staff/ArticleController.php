<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Menampilkan daftar artikel yang sudah dipublikasikan dalam format grid/card
     * Staff dapat melihat semua artikel published dengan fitur pencarian dan filter kategori
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

        return view('staff.articles.index', compact('articles', 'categories'));
    }

    /**
     * Menampilkan halaman manajemen artikel milik staff yang sedang login
     * Staff hanya dapat melihat dan mengelola artikel yang mereka buat sendiri
     */
    public function manage(Request $request)
    {
        $query = \App\Models\Article::with(['user', 'category'])
            ->where('user_id', auth()->id());

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $articles = $query->latest()->paginate(10);
        return view('staff.articles.manage', compact('articles'));
    }

    /**
     * Menampilkan detail artikel beserta komentar-komentarnya
     * Staff dapat melihat artikel published atau artikel draft milik sendiri
     * Otomatis menambah jumlah views artikel
     */
    public function show($slug)
    {
        $article = \App\Models\Article::with(['user', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        if ($article->status !== 'published' && $article->user_id !== auth()->id()) {
            abort(404);
        }

        $article->increment('views');

        $comments = $article->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->get();

        return view('staff.articles.show', compact('article', 'comments'));
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
     * Toggle like/unlike artikel oleh staff
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
     * Toggle bookmark/unbookmark artikel oleh staff
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

    /**
     * Menampilkan form untuk membuat artikel baru
     * Menyediakan daftar kategori aktif untuk dipilih
     */
    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        return view('staff.articles.create', compact('categories'));
    }

    /**
     * Menyimpan artikel baru ke database
     * Staff dapat membuat artikel dengan status draft atau published
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (\App\Models\Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
        }

        \App\Models\Article::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->input('content'),
            'excerpt' => \Illuminate\Support\Str::limit(strip_tags($request->input('content')), 150),
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'image' => $imagePath,
            'status' => $request->status,
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        $message = $request->status === 'published'
            ? 'Article created and published successfully.'
            : 'Article created successfully (Draft).';

        return redirect()->route('staff.articles.manage')->with('success', $message);
    }

    /**
     * Menampilkan form untuk mengedit artikel
     * Staff hanya dapat mengedit artikel milik sendiri
     */
    public function edit($slug)
    {
        $article = \App\Models\Article::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        return view('staff.articles.edit', compact('article', 'categories'));
    }

    /**
     * Memperbarui artikel yang sudah ada
     * Staff hanya dapat mengedit artikel milik sendiri
     * Staff dapat memilih status artikel (draft atau published)
     */
    public function update(Request $request, $slug)
    {
        $article = \App\Models\Article::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->title !== $article->title) {
            $slug = \Illuminate\Support\Str::slug($request->title);
            $originalSlug = $slug;
            $count = 1;
            while (\App\Models\Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $article->slug = $slug;
        }

        if ($request->hasFile('image')) {
            if ($article->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($article->image);
            }
            $article->image = $request->file('image')->store('articles', 'public');
        }

        $article->title = $request->title;
        $article->content = $request->input('content');
        $article->excerpt = \Illuminate\Support\Str::limit(strip_tags($request->input('content')), 150);
        $article->category_id = $request->category_id;
        $article->status = $request->status;

        // Update published_at berdasarkan status
        if ($request->status === 'published' && $article->published_at === null) {
            $article->published_at = now();
        } elseif ($request->status === 'draft') {
            $article->published_at = null;
        }

        $article->save();

        return redirect()->route('staff.articles.manage')->with('success', 'Article updated successfully.');
    }

    /**
     * Publish artikel yang berstatus draft
     * Staff dapat mempublikasikan artikel milik sendiri
     */
    public function publish($slug)
    {
        $article = \App\Models\Article::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();

        if ($article->status === 'published') {
            return back()->with('info', 'Article is already published.');
        }

        $article->status = 'published';
        $article->published_at = now();
        $article->save();

        return back()->with('success', 'Article published successfully.');
    }

    /**
     * Unpublish artikel yang berstatus published (kembali ke draft)
     * Staff dapat mengubah artikel published menjadi draft
     */
    public function unpublish($slug)
    {
        $article = \App\Models\Article::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();

        if ($article->status === 'draft') {
            return back()->with('info', 'Article is already in draft.');
        }

        $article->status = 'draft';
        $article->published_at = null;
        $article->save();

        return back()->with('success', 'Article unpublished successfully.');
    }

    /**
     * Menghapus artikel dari database
     * Staff hanya dapat menghapus artikel milik sendiri
     * Menghapus gambar artikel dari storage jika ada
     */
    public function destroy($slug)
    {
        $article = \App\Models\Article::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();
        if ($article->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($article->image);
        }
        $article->delete();

        return redirect()->route('staff.articles.manage')->with('success', 'Article deleted successfully.');
    }
}
