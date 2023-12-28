<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Helper;
use App\Traits\{DateTime, Prunable};

class OlsAccessLog extends Model
{
    use HasFactory, Prunable, DateTime; // Using Laravel's HasFactory, Prunable, and DateTime traits

    protected $table = 'ols_access_logs'; // Define the table associated with this model

    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'ip',
        'time',
        'url',
        'status',
        'bytes',
        'referrer_url',
        'referrer_domain',
        'is_bot_request',
        'is_sitemap_url',
        'is_robots_txt',
        'is_xmlrpc_request',
        'platform',
        'platform_version',
        'device',
        'bot_name',
        'method',
        'browser',
        'mime_type',
        'document_type',
        'protocol',
        'country',
        'state',
        'city',
    ]; // Fields that are allowed to be mass-assigned

    protected $dates = ['created_at', 'updated_at']; // Fields treated as dates for automatic conversion

    // Relationship with server: Each log belongs to a server
    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    // Relationship with application: Each log belongs to an application
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}