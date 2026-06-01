<?php

namespace Ssh521\LaravelAdmin\Models;

use Database\Factories\AdminNoticeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminNotice extends Model
{
    /** @use HasFactory<AdminNoticeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'author_id',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AdminNoticeAttachment::class, 'notice_id')->orderBy('sort_order');
    }
}
