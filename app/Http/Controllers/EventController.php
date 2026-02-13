<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // GET /api/events
    public function index(): JsonResponse
    {
        $events = Event::with(['creator:id,name,email'])
            ->withCount('participants')
            ->orderBy('event_date', 'asc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar event berhasil diambil.',
            'data'    => EventResource::collection($events->items()),
            'meta'    => [
                'current_page' => $events->currentPage(),
                'last_page'    => $events->lastPage(),
                'per_page'     => $events->perPage(),
                'total'        => $events->total(),
            ],
        ], 200);
    }

    // POST /api/events
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'event_date'  => 'required|date|after:now',
        ]);

        $event = Event::create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'event_date'  => $validated['event_date'],
            'user_id'     => $request->user()->id,
        ]);

        $event->load('creator:id,name,email');
        $event->loadCount('participants');

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil dibuat.',
            'data'    => new EventResource($event),
        ], 201);
    }

    // GET /api/events/{id}
    public function show(int $id): JsonResponse
    {
        $event = Event::with(['creator:id,name,email', 'participants:id,name,email'])
            ->withCount('participants')
            ->find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail event berhasil diambil.',
            'data'    => new EventResource($event),
        ], 200);
    }

    // POST /api/events/{id}/join
    public function join(Request $request, int $id): JsonResponse
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event tidak ditemukan.', 'data' => null], 404);
        }

        $user = $request->user();

        if ($event->user_id === $user->id) {
            return response()->json(['success' => false, 'message' => 'Kamu adalah creator event ini.', 'data' => null], 422);
        }

        if ($event->event_date->isPast()) {
            return response()->json(['success' => false, 'message' => 'Event ini sudah berakhir.', 'data' => null], 422);
        }

        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah terdaftar di event ini.', 'data' => null], 422);
        }

        $event->participants()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil bergabung ke event.',
            'data'    => [
                'event_id'           => $event->id,
                'event_title'        => $event->title,
                'event_date'         => $event->event_date->format('Y-m-d H:i:s'),
                'user_id'            => $user->id,
                'user_name'          => $user->name,
                'joined_at'          => now()->format('Y-m-d H:i:s'),
                'total_participants' => $event->participants()->count(),
            ],
        ], 200);
    }
}