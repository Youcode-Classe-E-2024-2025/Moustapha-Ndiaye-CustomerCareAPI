<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'field_changed', // e.g., 'status', 'assigned_to', 'priority'
        'old_value',
        'new_value',
        'action_description',
    ];
    
    /**
     * Get the ticket that this history entry belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    /**
     * Get the user who made this change.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}