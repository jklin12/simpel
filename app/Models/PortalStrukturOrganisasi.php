<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortalStrukturOrganisasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jabatan',
        'foto',
        'parent_id',
        'urutan',
    ];

    /**
     * Get the parent.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PortalStrukturOrganisasi::class, 'parent_id');
    }

    /**
     * Get the children.
     */
    public function children(): HasMany
    {
        return $this->hasMany(PortalStrukturOrganisasi::class, 'parent_id')->orderBy('urutan', 'asc');
    }

    /**
     * Delete children automatically when parent is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            foreach ($model->children as $child) {
                // This triggers the deleting event for each child, allowing for full cascaded deletes.
                $child->delete();
            }
        });
    }
}
