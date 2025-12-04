<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssueComment extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'comment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'issue_id',
        'user_id',
        'comment',
        'is_resolution',
    ];

    protected $casts = [
        'is_resolution' => 'boolean',
    ];

    // Relationships
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'issue_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IssueAttachment::class, 'comment_id', 'comment_id');
    }
}
