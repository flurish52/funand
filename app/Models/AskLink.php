<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AskLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'ask_id',
        'slug',
        'share_text',
        'clicks',
    ];

    public function ask(): BelongsTo
    {
        return $this->belongsTo(Ask::class);
    }
}
