<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // If you want to specify the columns that are mass assignable
    protected $fillable = ['name', 'description'];
}
