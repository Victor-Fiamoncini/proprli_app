<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'building_id' => $this->buildingId,
            'assigned_user_id' => $this->assignedUserId,
            'creator_user_id' => $this->creatorUserId,
            'comments' => CommentResource::collection($this->comments),
        ];
    }
}
