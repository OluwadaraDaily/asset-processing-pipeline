<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'upload_id',
        'uuid',
        'original_filename',
        'path',
        'status',
        'error_message',
        'original_width',
        'original_height',
        'target_width',
        'target_height',
    ];

    protected static function booted(): void
    {
        static::creating(function (Image $image) {
            // Only auto-generate UUID if not provided (frontend should provide it)
            if (! $image->uuid) {
                $image->uuid = (string) Str::uuid();
            }
        });
    }

    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }
}
