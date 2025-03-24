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
     * Get all tickets with pagination and filters
     *
     * @param array $filters
     * @return LengthAwarePaginator
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
     * Get a ticket by ID
     *
     * @param int $id
     * @return Ticket
     */
    public function getTicket(int $id): Ticket
    {
        $user = Auth::user();
        $ticket = Ticket::with([
            'creator', 
            'assignedAgent', 
            'status', 
            'responses' => function($query) use ($user) {
                // Only show internal responses to agents and admins
                if ($user && !$user->isClient()) {
                    return $query;
                }
                return $query->where('is_internal', false);
            },
            'responses.user',
            'attachments'
        ])->findOrFail($id);

        // Check if user has access to this ticket
        if ($user && $user->isClient() && $ticket->creator_id !== $user->id) {
            throw new \Illuminate\Auth\Access\AuthorizationException('You do not have permission to view this ticket.');
        }

        return $ticket;
    }

    /**
     * Create a new ticket
     *
     * @param array $data
     * @return Ticket
     */
    public function createTicket(array $data): Ticket
    {
        // Set default status to 'New' if not provided
        if (!isset($data['status_id'])) {
            $newStatus = Status::where('name', 'New')->first();
            $data['status_id'] = $newStatus ? $newStatus->id : 1;
        }

        // Set creator_id to current user if not specified
        if (!isset($data['creator_id'])) {
            $data['creator_id'] = Auth::id();
        }

        $ticket = Ticket::create($data);

        // Create history entry for ticket creation
        $this->createHistoryEntry($ticket, 'created', null, 'created', 'Ticket created');

        return $ticket;
    }

    /**
     * Update an existing ticket
     *
     * @param int $id
     * @param array $data
     * @return Ticket
     */
    public function updateTicket(int $id, array $data): Ticket
    {
        $ticket = Ticket::findOrFail($id);
        $oldValues = $ticket->toArray();

        $ticket->update($data);

        // Create history entries for changes
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

    /**
     * Delete a ticket
     *
     * @param int $id
     * @return bool
     */
    public function deleteTicket(int $id): bool
    {
        $ticket = Ticket::findOrFail($id);
        return $ticket->delete();
    }

    /**
     * Assign a ticket to an agent
     *
     * @param int $ticketId
     * @param int $agentId
     * @return Ticket
     */
    public function assignTicket(int $ticketId, int $agentId): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);
        $agent = User::findOrFail($agentId);

        // Check if user is an agent
        if (!$agent->isAgent() && !$agent->isAdmin()) {
            throw new \InvalidArgumentException('User is not an agent or admin');
        }

        $oldAgent = $ticket->assigned_to;
        
        // Update ticket
        $ticket->assigned_to = $agentId;
        
        // If ticket was just assigned for the first time, update status to Open
        if (!$oldAgent) {
            $openStatus = Status::where('name', 'Open')->first();
            if ($openStatus) {
                $ticket->status_id = $openStatus->id;
                $this->createHistoryEntry(
                    $ticket, 
                    'status_id', 
                    $ticket->status_id, 
                    $openStatus->id, 
                    'Status changed to Open upon assignment'
                );
            }
        }
        
        $ticket->save();

        // Create history entry
        $oldAgentName = $oldAgent ? User::find($oldAgent)->name : 'Unassigned';
        $this->createHistoryEntry(
            $ticket,
            'assigned_to',
            $oldAgent,
            $agentId,
            "Assigned from '$oldAgentName' to '{$agent->name}'"
        );

        return $ticket->fresh();
    }

    /**
     * Change ticket status
     *
     * @param int $ticketId
     * @param int $statusId
     * @return Ticket
     */
    public function changeStatus(int $ticketId, int $statusId): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);
        $status = Status::findOrFail($statusId);
        
        $oldStatusId = $ticket->status_id;
        $oldStatus = Status::find($oldStatusId);
        
        $ticket->status_id = $statusId;
        
        // If status changed to Resolved, update is_resolved and resolved_at
        if ($status->name === 'Resolved' && $oldStatus->name !== 'Resolved') {
            $ticket->is_resolved = true;
            $ticket->resolved_at = now();
        }
        
        // If status changed from Resolved to something else, update is_resolved
        if ($status->name !== 'Resolved' && $oldStatus->name === 'Resolved') {
            $ticket->is_resolved = false;
            $ticket->resolved_at = null;
        }
        
        $ticket->save();

        // Create history entry
        $this->createHistoryEntry(
            $ticket,
            'status_id',
            $oldStatusId,
            $statusId,
            "Status changed from '{$oldStatus->name}' to '{$status->name}'"
        );

        return $ticket->fresh();
    }

    /**
     * Create a ticket history entry
     *
     * @param Ticket $ticket
     * @param string $fieldChanged
     * @param mixed $oldValue
     * @param mixed $newValue
     * @param string $description
     * @return TicketHistory
     */
    protected function createHistoryEntry(
        Ticket $ticket,
        string $fieldChanged,
        $oldValue,
        $newValue,
        string $description
    ): TicketHistory {
        return TicketHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id() ?: 1, // Default to admin if not authenticated
            'field_changed' => $fieldChanged,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'action_description' => $description
        ]);
    }

    /**
     * Get ticket history
     *
     * @param int $ticketId
     * @return Collection
     */
    public function getTicketHistory(int $ticketId): Collection
    {
        $ticket = Ticket::findOrFail($ticketId);
        return $ticket->history()->with('user')->orderBy('created_at', 'desc')->get();
    }
}