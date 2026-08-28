<?php

namespace App\Http\Controllers;

use App\Models\Ask;
use Inertia\Inertia;

class AskResponseController extends Controller
{
    public function index(Ask $ask)
    {
        // Swap for a policy (Gate::authorize / $this->authorize) if you have one
        abort_unless($ask->user_id === auth()->id(), 403);

        $responses = $ask->responses()
            ->with('venue')
            ->latest('responded_at')
            ->get()
            ->map(fn ($response) => [
                'id' => $response->id,
                'decline_taps' => $response->decline_taps,
                'responded_date' => optional($response->responded_date)->toDateString(),
                'responded_time' => $response->responded_time,
                'responded_at' => $response->responded_at,
                'seconds_to_yes' => $response->responded_at
                    ? $response->created_at->diffInSeconds($response->responded_at)
                    : null,
                'venue' => $response->venue ? [
                    'name' => $response->venue->name,
                    'address' => $response->venue->address ?? null,
                ] : null,
            ]);

        return Inertia::render('Dashboard/Response/Index', [
            'ask' => [
                'id' => $ask->id,
                'for_name' => $ask->for_name,
                'question' => $ask->question,
            ],
            'responses' => $responses,
        ]);
    }
}
