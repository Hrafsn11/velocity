<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueAttachment extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'attachment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'issue_id',
        'comment_id',
        'uploaded_by',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'issue_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(IssueComment::class, 'comment_id', 'comment_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'user_id');
    }

    // Helper methods
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function isImage(): bool
    {
        return in_array($this->file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);
    }
}
