<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\Summary\Bandwidth;

class Server extends Model
{
    use HasFactory, DateTime;

    // Define the table name
    protected $table = 'servers';

    // Fields that can be mass-assigned
    protected $fillable = [
        'sa_organization_id',  // ID of the SA organization
        'sa_server_id',        // ID of the SA server
        'ip',                  // IP address of the server
        'name',                // Server name
        'operating_system',    // Operating system of the server
        'version',             // Version of the server
        'cores',               // Number of CPU cores
        'web_server',          // Web server software
        'agent_status',        // Status of an agent
        'timezone',             // Timezone of the server
        'database'              // Database type
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    // Define a relationship with Application model (assuming Application class exists)
    public function applications()
    {
        return $this->hasMany(Application::Class);
    }

    // Define a relationship with UserServer model
    public function userServers()
    {
        return $this->hasMany(UserServer::class);
    }

    // Define a relationship with Service model
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // Define a relationship with Usage model
    public function usages()
    {
        return $this->hasMany(Usage::class);
    }
}
