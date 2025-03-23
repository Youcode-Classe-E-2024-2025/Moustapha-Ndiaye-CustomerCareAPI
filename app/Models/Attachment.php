<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'filename',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];
    
    /**
     * Get the parent attachable model (ticket or response).
     */
    public function attachable()
    {
        return $this->morphTo();
    }
    
    /**
     * Get the user who uploaded this attachment.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}