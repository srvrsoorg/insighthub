<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Prunable;

class Throughput extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the Throughput model in the database
    protected $table = 'throughputs';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'total_request',
        'request_per',
        'calculation_per',
        'is_bot_request',
        'log_time',
    ];

    protected $dates = ['log_time'];

    // Relationship to 'Server' model: A Throughput belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Relationship to 'Application' model: A Throughput belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}