<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'event_date'  => $this->event_date?->format('Y-m-d H:i:s'),

            'creator' => $this->whenLoaded('creator', fn () => [
                'id'    => $this->creator->id,
                'name'  => $this->creator->name,
                'email' => $this->creator->email,
            ]),

            'total_participants' => $this->when(
                isset($this->participants_count),
                $this->participants_count ?? 0
            ),

            // Hanya muncul di detail (show), bukan list (index)
            'participants' => $this->whenLoaded('participants', fn () =>
                $this->participants->map(fn ($u) => [
                    'id' => $u->id, 'name' => $u->name, 'email' => $u->email,
                ])
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}