<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AskResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'ask_id',
        'ask_venue_id',
        'responded_date',
        'responded_time',
        'decline_taps',
        'responded_at',
        'ip_address',
    ];

    protected $casts = [
        'responded_date' => 'date',
        'responded_at' => 'datetime',
    ];

    public function ask(): BelongsTo
    {
        return $this->belongsTo(Ask::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(AskVenue::class, 'ask_venue_id');
    }

}
