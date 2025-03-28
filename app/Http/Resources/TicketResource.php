<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // public function toArray(Request $request): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'title' => $this->title,
    //         'description' => $this->description,
    //         'status' => new StatusResource($this->whenLoaded('status')),
    //         'creator' => new UserResource($this->whenLoaded('creator')),
    //         'assigned_agent' => new UserResource($this->whenLoaded('assignedAgent')),
    //         'priority' => $this->priority,
    //         'category' => $this->category,
    //         'due_date' => $this->due_date,
    //         'is_resolved' => $this->is_resolved,
    //         'resolved_at' => $this->resolved_at,
    //         'resolution_note' => $this->resolution_note,
    //         'responses' => ResponseResource::collection($this->whenLoaded('responses')),
    //         'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
    //         'created_at' => $this->created_at,
    //         'updated_at' => $this->updated_at,
    //     ];
    // }
    public function toArray(Request $request): array
    {
        // return [
        //     'id' => $this->id,
        //     'title' => $this->title,
        //     'description' => $this->description,
        //     'status' => new StatusResource($this->whenLoaded('status')),
        //     'creator' => new UserResource($this->whenLoaded('creator')),
        //     'assigned_agent' => new UserResource($this->whenLoaded('assignedAgent')),
        //     'priority' => $this->priority,
        //     'category' => $this->category,
        //     'due_date' => $this->due_date,
        //     'is_resolved' => $this->is_resolved,
        //     'resolved_at' => $this->is_resolved ? $this->resolved_at : null,
        //     'resolution_note' => $this->is_resolved ? $this->resolution_note : null,
        //     'responses' => ResponseResource::collection($this->whenLoaded('responses')),
        //     'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
        //     'created_at' => $this->created_at,
        //     'updated_at' => $this->updated_at,
        // ];
      
            return [
                'id' => $this->id,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'category' => $this->category,
                'status' => new StatusResource($this->status),
                'assigned_to' => $this->assignedAgent ? $this->assignedAgent->name : null,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        
    }

}