<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;

class Database extends Model
{
    use HasFactory, DateTime;

    // Define the table name
    protected $table = "databases";

    // Fields that can be mass-assigned
    protected $fillable = [
        'host',     // The host of the database server
        'database', // The name of the database
        'username', // The username for database access
        'password'  // The password for database access
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];
}
