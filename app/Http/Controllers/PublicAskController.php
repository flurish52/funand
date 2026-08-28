<?php

namespace App\Http\Controllers;

use App\Models\AskLink;
use App\Http\Requests\StoreAskResponseRequest;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The public, no-auth-required side of the app.
 * Resolves /for-{slug} to an Ask and records the recipient's response.
 */
class PublicAskController extends Controller
{
    public function show(string $slug)
    {
        $link = AskLink::with('ask.venues')->where('slug', $slug)->firstOrFail();
        $ask = $link->ask;

        abort_unless($ask->is_published, 404);

        return Inertia::render('Public/Ask/Show', [
            'ask' => [
                    'id' => $ask->id,
                    'for_name' => $ask->for_name,
                    'type' => $ask->type,
                    'cover_image' => $ask->cover_image ? asset('storage/' . $ask->cover_image) : null,
                    'question' => $ask->question,
                    'accept_label' => $ask->accept_label,
                    'decline_label' => $ask->decline_label,
                    'has_scheduling_step' => $ask->has_scheduling_step,
                    'scheduling_heading' => $ask->scheduling_heading,
                    'min_date' => $ask->min_date?->toDateString(),
                    'max_date' => $ask->max_date?->toDateString(),
                    'final_heading' => $ask->final_heading ?? 'Hurray!',
                    'final_message' => $ask->final_message ?? 'I knew you would say yes!',
                    'creator_name' => $ask->user->username,
                'venues' => $ask->venues->map(fn ($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'address' => $v->address,
                ]),
            ],
            'slug' => $slug,
        ]);
    }

    public function respond(StoreAskResponseRequest $request, string $slug): RedirectResponse
    {
        $link = AskLink::with('ask')->where('slug', $slug)->firstOrFail();
        $ask = $link->ask;

        abort_unless($ask->is_published, 404);

        $ask->responses()->create([
            ...$request->validated(),
            'responded_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('responded', true);
    }
}
