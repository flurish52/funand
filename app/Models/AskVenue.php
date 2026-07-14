<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AskVenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'ask_id',
        'name',
        'address',
        'sort_order',
    ];

    public function ask(): BelongsTo
    {
        return $this->belongsTo(Ask::class);
    }
}
