<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application};
use App\Traits\{DateTime, Prunable};

class Url extends Model
{
    use HasFactory, DateTime, Prunable;

    // Define the table associated with the Url model in the database
    protected $table = 'urls';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'url',
        'title',
        'browser',
        'method',
        'status',
        'bot_name',
        'is_bot_request',
        'is_sitemap_url',
        'is_xmlrpc_request',
        'count',
    ];

    // Relationship to 'Server' model: A Url belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Relationship to 'Application' model with default value: A Url belongs to an Application (with default value)
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}