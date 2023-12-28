<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helper;
use App\Traits\{DateTime, Prunable};

class Service extends Model
{
    use HasFactory, Prunable, DateTime;

    // Set the database table for this model
    protected $table = "services";

    // Define the fields that are fillable
    protected $fillable = [
        'server_id',
        'name',  
        'status',
        'cpu_usage',
        'memory_usage'
    ];

    // Define the fields that are treated as dates
    protected $dates = ['created_at', 'updated_at'];

    // Define a relationship with the Server model
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}