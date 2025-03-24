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
 
}