<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helper;
use App\Traits\{DateTime, Prunable};

class Usage extends Model
{
    use HasFactory, Prunable, DateTime;

    // Set the database table for this model
    protected $table = "usages";

    // Define the fields that are fillable
    protected $fillable = [
        'server_id',
        'five_min_load',  
        'fifteen_min_load',
        'memory_in_pr',
        'disk_in_pr',
        'swap_in_pr'
    ];

    // Define the fields that are treated as dates
    protected $dates = ['created_at', 'updated_at'];

    // Define a relationship with the Server model
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}