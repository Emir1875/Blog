<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Check Role Admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    //Check Role Staff
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    //Check Role Pelanggan
    public function isPelanggan()
    {
        return $this->role === 'pelanggan';
    }

    public function likedArticles()
    {
        return $this->belongsToMany(Article::class, 'article_likes')
            ->withPivot('created_at')
            ->withTimestamps();
    }

    public function bookmarkedArticles()
    {
        return $this->belongsToMany(Article::class, 'bookmarks')
            ->withPivot('created_at')
            ->withTimestamps();
    }
}
