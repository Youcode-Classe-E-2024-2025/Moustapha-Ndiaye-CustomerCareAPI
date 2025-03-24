<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Status;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TicketService
{
    /**
     * Get all tickets with pagination and filter
     * 
     * @param array $filter
     * 
     * @return LengthAwarePagination
     */

     public function getAllTickets(array $filters = []): LengthAwarePaginator
     {
         $query = Ticket::with(['creator', 'assignedAgent', 'status']);
 
         // Apply filters
         if (isset($filters['status_id']) && $filters['status_id']) {
             $query->where('status_id', $filters['status_id']);
         }
 
         if (isset($filters['priority']) && $filters['priority']) {
             $query->where('priority', $filters['priority']);
         }
 
         if (isset($filters['assigned_to']) && $filters['assigned_to']) {
             $query->where('assigned_to', $filters['assigned_to']);
         }
 
         if (isset($filters['category']) && $filters['category']) {
             $query->where('category', $filters['category']);
         }
 
         // Filter by creator if the user is a client
         $user = Auth::user();
         if ($user && $user->isClient()) {
             $query->where('creator_id', $user->id);
         }
 
         // Apply sorting
         $sortField = $filters['sort_field'] ?? 'created_at';
         $sortDirection = $filters['sort_direction'] ?? 'desc';
         $query->orderBy($sortField, $sortDirection);
 
         // Paginate results
         $perPage = $filters['per_page'] ?? 15;
         
         return $query->paginate($perPage);
     }

     /**
        * Get ticket by id 
        *@param it $id
        *@return Ticket
      */

    public function getTicket(int $id): Ticket {
        // get user 
        $user = Auth::user();
        // get ticket 
        $ticket = Ticket::with(
       [
        'creator', 
        'assignedAgent', 
        'status',
        'responses' => function($query) use ($user){
            if ($user && !isClient()){
                return $query;
            }
            return $query->where('is_internal', false);
        },
        'responses.user',
        'attachments'
       ]
        )->findOrFail($id);

        // check if user has acess to his ticket 
        if ($user && isClient() && $ticket->creator_id !== $user->id){
            throw new \Illuminate\Auth\Access\AuthorizationException('You do not have permission to view this ticket.');
        }

        return $ticket;
    }

    /**
     * Create a new ticket 
     * @param array $data
     * @return ticket
     */
    public function createTicket(array $data): Ticket {
        // set default status if not provided
        if (!$data['status_id']){
            $newStatus = Status::where('name', 'New')->first();
            $data['status_id'] = $newStatus ? $newStatus : 1;
        }

        //set creator_id to current user id not specified
        if (!$data['creator_id']){
            $data['creator_id'] = Auth::id();
        }

        $ticket = Ticket::create($data);

        // create history entry for tickect reservation
        $this->createHistoryEntry($ticket, 'created', null, 'created', 'Ticket created');

        return $ticket;
    }
 
    /**
     * Update existing ticket 
     * @param int $id
     * @param array $data
     * @return ticket 
     */

    public function updateTicket(array $data, int $id): Ticket {
        $ticket = Ticket::findOrFail($id);
        $oldValues = $ticket->array();

        $ticket->update($data);

        //history changes for entries
        foreach ($data as $field => $value) {
            if (isset($oldValues[$field]) && $oldValues[$field] != $value) {
                $this->createHistoryEntry(
                    $ticket,
                    $field,
                    $oldValues[$field],
                    $value,
                    "Changed $field from '{$oldValues[$field]}' to '$value'"
                );
            }
        }

        return $ticket->fresh();
    }
}