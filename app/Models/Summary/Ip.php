<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application};
use App\Traits\Prunable;

class Ip extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the Ip model in the database
    protected $table = 'ips';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'ip',
        'url',
        'bandwidth',
        'is_bot_request',
        'count'
    ];

    // Relationship to 'Server' model: An Ip belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Relationship to 'Application' model: An Ip belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}