<?php

namespace App\Http\Controllers;

use App\Models\Ask;
use App\Http\Requests\StoreAskRequest;
use App\Http\Requests\UpdateAskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Dashboard-side CRUD for the authenticated user's own asks.
 * Nothing here is publicly reachable — see PublicAskController for that.
 */
class AskController extends Controller
{
    public function index(Request $request): Response
    {
        $asks = Ask::query()
            ->where('user_id', Auth::id())
            ->withCount('responses')
            ->with(['response.venue'])
            ->latest()
            ->get()
            ->map(function (Ask $ask) {
                $response = $ask->response;

                return [
                    'id' => $ask->id,
                    'for_name' => $ask->for_name,
                    'question' => $ask->question,
                    'is_published' => $ask->is_published,
                    'responses_count' => $ask->responses_count,
                    'slug' => $ask->primaryLink()?->slug,
                    'share_text' => $ask->primaryLink()?->share_text,
                    'cover_image' => $ask->cover_image,
                    'created_at' => $ask->created_at->diffForHumans(),
                    'response_status' => match (true) {
                        $response && $response->responded_at => 'answered',
                        $response => 'opened',
                        default => 'pending',
                    },

                    'response' => $response ? [
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
                    ] : null,
                ];
            });

        return Inertia::render('Dashboard/Asks/Index', [
            'asks' => $asks,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Dashboard/Asks/Form', [
            'ask' => null,
        ]);
    }

    public function store(StoreAskRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('ask-covers', 'public');
        }

        $ask = $request->user()->asks()->create([
            ...$data,
            'is_published' => $data['is_published'] ?? false,
        ]);

        foreach ($data['venues'] ?? [] as $index => $venue) {
            $ask->venues()->create([
                'name' => $venue['name'],
                'address' => $venue['address'] ?? null,
                'sort_order' => $index,
            ]);
        }

        $ask->links()->create([
            'slug' => $this->generateUniqueSlug(),
            'share_text' => $data['share_text'] ?? null,
        ]);

        return redirect()
            ->route('asks.index')
            ->with('success', 'Your ask has been created!');
    }

    /**
     * Generate a short, URL-safe slug and guarantee it doesn't collide with
     * an existing ask link. Retries on the rare collision rather than
     * trusting randomness alone.
     */
    private function generateUniqueSlug(int $length = 8): string
    {
        do {
            $slug = Str::lower(Str::random($length));
        } while (\App\Models\AskLink::where('slug', $slug)->exists());

        return $slug;
    }

    public function edit(Ask $ask): Response
    {
        $this->authorize('update', $ask);

        $ask->load(['venues', 'links']);

        return Inertia::render('Dashboard/Asks/Form', [
            'ask' => [
                ...$ask->toArray(),
                'slug' => $ask->primaryLink()?->slug,
                'share_text' => $ask->primaryLink()?->share_text,
            ],
        ]);
    }

    public function update(UpdateAskRequest $request, Ask $ask)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($ask->cover_image) {
                Storage::disk('public')->delete($ask->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('ask-covers', 'public');
        }

        $ask->update($data);

        // Simple sync: wipe and re-create venues from the submitted list
        $ask->venues()->delete();
        foreach ($data['venues'] ?? [] as $index => $venue) {
            $ask->venues()->create([
                'name' => $venue['name'],
                'address' => $venue['address'] ?? null,
                'sort_order' => $index,
            ]);
        }

        $link = $ask->primaryLink();
        if ($link) {
            $link->update([
                'share_text' => $data['share_text'] ?? null,
            ]);
        } else {
            $ask->links()->create([
                'share_text' => $data['share_text'] ?? null,
            ]);
        }

        return redirect()
            ->route('asks.index')
            ->with('success', 'Your ask has been updated!');
    }

    public function destroy(Ask $ask)
    {
        $this->authorize('delete', $ask);

        if ($ask->cover_image) {
            Storage::disk('public')->delete($ask->cover_image);
        }

        $ask->delete();

        return redirect()
            ->route('asks.index')
            ->with('success', 'Ask deleted.');
    }

    /** Quick publish/unpublish toggle, used by a switch in the dashboard list. */
    public function togglePublish(Ask $ask)
    {
        $this->authorize('update', $ask);

        $ask->update(['is_published' => ! $ask->is_published]);

        return back();
    }
}
