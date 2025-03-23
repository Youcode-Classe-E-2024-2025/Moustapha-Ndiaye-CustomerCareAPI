<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'creator_id',
        'assigned_to',
        'status_id',
        'priority',
        'category',
        'due_date',
        'is_resolved',
        'resolved_at',
        'resolution_note',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'is_resolved' => 'boolean',
    ];
    
    /**
     * Get the user who created the ticket.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    
    /**
     * Get the agent assigned to the ticket.
     */
    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get the status of the ticket.
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    
    /**
     * Get all responses for this ticket.
     */
    public function responses()
    {
        return $this->hasMany(Response::class);
    }
    
    /**
     * Get all history entries for this ticket.
     */
    public function history()
    {
        return $this->hasMany(TicketHistory::class);
    }
    
    /**
     * Get all attachments for this ticket.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}