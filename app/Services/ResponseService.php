<?php

namespace App\Services;

use App\Models\Response;
use App\Models\Ticket;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponseService
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Get all responses for a ticket with pagination
     *
     * @param int $ticketId
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getTicketResponses(int $ticketId, array $filters = []): LengthAwarePaginator
    {
        $query = Response::with(['user'])
            ->where('ticket_id', $ticketId);

        // User is a client, filter out internal responses
        if (Auth::user() && Auth::user()->isClient()) {
            $query->where('is_internal', false);
        }

        // Apply sorting
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $filters['per_page'] ?? 5;
        
        return $query->paginate($perPage);
    }

    /**
     * Get a response by ID
     *
     * @param int $id
     * @return Response
     */
    public function getResponse(int $id): Response
    {
        $response = Response::with(['user', 'ticket'])->findOrFail($id);
        
        // Check if user has access to this response
        if (Auth::user() && Auth::user()->isClient() && $response->is_internal) {
            throw new \Illuminate\Auth\Access\AuthorizationException('You do not have permission to view this response.');
        }
        
        return $response;
    }

    /**
     * Create a new response
     *
     * @param array $data
     * @return Response
     */
    public function createResponse(array $data): Response
    {
        // Ensure ticket exists
        $ticket = Ticket::findOrFail($data['ticket_id']);
        
        // Set user_id to current user if not specified
        if (!isset($data['user_id'])) {
            $data['user_id'] = Auth::id();
        }
        
        // Create the response
        $response = Response::create($data);
        
        // If this is a resolution response, update ticket status
        if (isset($data['is_resolution']) && $data['is_resolution']) {
            $resolvedStatus = Status::where('name', 'Resolved')->first();
            if ($resolvedStatus) {
                $this->ticketService->changeStatus($ticket->id, $resolvedStatus->id);
            }
        }
        
        // Update ticket status to In Progress if not already 
        // and response is from an agent and ticket is in Open status
        if (!$data['is_internal'] && !isset($data['is_resolution']) && Auth::user() && !Auth::user()->isClient()) {
            $openStatus = Status::where('name', 'Open')->first();
            $inProgressStatus = Status::where('name', 'In Progress')->first();
            
            if ($ticket->status_id == $openStatus->id && $inProgressStatus) {
                $this->ticketService->changeStatus($ticket->id, $inProgressStatus->id);
            }
        }
        
        return $response;
    }

    /**
     * Update an existing response
     *
     * @param int $id
     * @param array $data
     * @return Response
     */
    public function updateResponse(int $id, array $data): Response
    {
        $response = Response::findOrFail($id);
        
        // Verify user is allowed to update this response
        if (Auth::user() && Auth::user()->isClient() && $response->user_id != Auth::id()) {
            throw new \Illuminate\Auth\Access\AuthorizationException('You can only edit your own responses.');
        }
        
        $response->update($data);
        
        // Handle resolution status change if needed
        if (isset($data['is_resolution']) && $data['is_resolution'] != $response->is_resolution) {
            $ticket = $response->ticket;
            if ($data['is_resolution']) {
                $resolvedStatus = Status::where('name', 'Resolved')->first();
                if ($resolvedStatus) {
                    $this->ticketService->changeStatus($ticket->id, $resolvedStatus->id);
                }
            }
        }
        
        return $response->fresh();
    }

    /**
     * Delete a response
     *
     * @param int $id
     * @return bool
     */
    public function deleteResponse(int $id): bool
    {
        $response = Response::findOrFail($id);
        
        // Verify user is allowed to delete this response
        if (Auth::user() && Auth::user()->isClient() && $response->user_id != Auth::id()) {
            throw new \Illuminate\Auth\Access\AuthorizationException('You can only delete your own responses.');
        }
        
        return $response->delete();
    }
}