<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application};
use App\Traits\Prunable;

class Browser extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the Browser model in the database
    protected $table = 'browsers';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'browser',
        'is_bot_request',
        'count',
    ];

    // Relationship to 'Server' model: A Browser belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }
    
    // Relationship to 'Application' model: A Browser belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}