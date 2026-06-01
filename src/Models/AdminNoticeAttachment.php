<?php

namespace Ssh521\LaravelAdmin\Models;

use Database\Factories\AdminNoticeAttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNoticeAttachment extends Model
{
    /** @use HasFactory<AdminNoticeAttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'notice_id',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function notice(): BelongsTo
    {
        return $this->belongsTo(AdminNotice::class, 'notice_id');
    }

    /**
     * 파일 URL 가져오기
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->file_path);
    }

    /**
     * 파일 크기를 읽기 쉬운 형식으로 변환
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
