<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PortalBerita extends Model
{
    protected $table = 'portal_beritas';

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'thumbnail',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Scope untuk berita yang sudah dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc');
    }

    /**
     * Auto-generate slug dari judul jika belum diset.
     */
    public static function generateSlug(string $judul): string
    {
        $slug = Str::slug($judul);
        $count = static::where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
