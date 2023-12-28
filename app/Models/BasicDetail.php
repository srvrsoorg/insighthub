<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasicDetail extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'basic_details';

    // Fields that can be mass-assigned
    protected $fillable = [
        'key',   // The key for the basic detail
        'value' // The value associated with the key
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    // Define the casting of the 'value' field as an array
    protected $casts = [
        'value' => 'array',
    ];
}
