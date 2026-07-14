<?php

namespace App\Http\Controllers;

use App\Models\Ask;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AskResponseController extends Controller
{
    /**
     * List every ask belonging to the logged-in user, with response data
     * inlined so the frontend can open a modal per row without a round trip.
     */
    public function index(): Response
    {
        $asks = Ask::query()
            ->where('user_id', Auth::id())
            ->with(['response.venue', 'askLink'])
            ->latest()
            ->get()
            ->map(fn (Ask $ask) => $this->transform($ask));

        return Inertia::render('Dashboard/Asks/Response', [
            'asks' => $asks,
        ]);
    }

    private function transform(Ask $ask): array
    {
        $response = $ask->response;

        return [
            'id' => $ask->id,
            'for_name' => $ask->for_name,
            'type' => $ask->type,
            'question' => $ask->question,
            'accept_label' => $ask->accept_label,
            'decline_label' => $ask->decline_label,
            'has_scheduling_step' => $ask->has_scheduling_step,
            'is_published' => $ask->is_published,
            'created_at' => $ask->created_at,
            'share_url' => $ask->link
                ? route('asks.public', $ask->link->token)
                : null,

            'status' => match (true) {
                $response && $response->responded_at => 'answered',
                $response => 'opened',
                default => 'pending',
            },

            'response' => $response ? [
                'decline_taps' => $response->decline_taps,
                'responded_date' => optional($response->responded_date)->toDateString(),
                'responded_time' => $response->responded_time,
                'responded_at' => $response->responded_at,
                'opened_at' => $response->created_at,
                'seconds_to_yes' => $response->responded_at
                    ? $response->created_at->diffInSeconds($response->responded_at)
                    : null,
                'venue' => $response->venue ? [
                    'name' => $response->venue->name,
                    'address' => $response->venue->address ?? null,
                ] : null,
            ] : null,
        ];
    }
}
