<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ask extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'for_name',
        'type',
        'cover_image',
        'question',
        'accept_label',
        'decline_label',
        'has_scheduling_step',
        'scheduling_heading',
        'min_date',
        'max_date',
        'final_heading',
        'final_message',
        'is_published',
        'ask_venue_id',
        'responded_date',
        'responded_time',
        'decline_taps',
    ];

    protected $casts = [
        'has_scheduling_step' => 'boolean',
        'is_published' => 'boolean',
        'min_date' => 'date',
        'max_date' => 'date',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(AskVenue::class)->orderBy('sort_order');
    }

    public function response(): HasOne
    {
        return $this->hasOne(AskResponse::class);
    }

    public function askLink(): HasOne
    {
        return $this->hasOne(AskLink::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(AskResponse::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(AskLink::class);
    }

    /** Convenience accessor for the ask's primary/most recent share link. */
    public function primaryLink(): ?AskLink
    {
        return $this->links()->latest()->first();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
