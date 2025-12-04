<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RiskActivity extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'subject_type',
        'subject_id',
        'activity_type',
        'description',
        'changes',
        'user_id',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // Relationships
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Methods
    public function getFormattedChanges(): string
    {
        if (empty($this->changes)) {
            return '';
        }

        $formatted = [];
        foreach ($this->changes as $field => $values) {
            if (isset($values['from']) && isset($values['to'])) {
                $formatted[] = ucfirst($field) . ": {$values['from']} → {$values['to']}";
            }
        }

        return implode(', ', $formatted);
    }
}
