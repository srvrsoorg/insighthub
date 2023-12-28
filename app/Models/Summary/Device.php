<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application};
use App\Traits\Prunable;

class Device extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the Device model in the database
    protected $table = 'devices';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'device',
        'is_bot_request',
        'count',
    ];

    // Relationship to 'Server' model: A Device belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    
    // Relationship to 'Application' model: A Device belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}