<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application};
use App\Traits\Prunable;

class PlatformVersion extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the PlatformVersion model in the database
    protected $table = 'platform_versions';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'platform',
        'platform_version',
        'is_bot_request',
        'count',
    ];

    // Relationship to 'Server' model: A PlatformVersion belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Relationship to 'Application' model: A PlatformVersion belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}