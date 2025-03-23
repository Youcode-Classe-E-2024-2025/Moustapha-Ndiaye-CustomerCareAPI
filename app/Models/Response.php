<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
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
        'content',
        'is_internal', // true for agent notes, false for client-visible responses
        'is_resolution',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_internal' => 'boolean',
        'is_resolution' => 'boolean',
    ];
    
    /**
     * Get the ticket that this response belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    /**
     * Get the user who created this response.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get all attachments for this response.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}