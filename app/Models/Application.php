<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DateTime;
use App\Models\Summary\{Bandwidth, Bot, Browser, Device, Ip, Method, MimeType, Platform, PlatformVersion, Protocol, Status, Url, Referrer};

class Application extends Model
{
    use HasFactory, DateTime;

    // Define the table name
    protected $table = 'applications';

    // Fields that can be mass-assigned
    protected $fillable = [
        'server_id',        // Associated server ID
        'sa_application_id',// SA application ID
        'framework',        // Application framework
        'name',             // Application name
        'primary_domain',   // Application domain
        'php_version',       // Php verison
        'ssl',               // Ssl ('Custom/Automatic')
        'size',              // Application size in mb,
        'active'            // Application status enable/disable
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    // Hidden fields (not serialized when converted to JSON)
    protected $hidden = ['pivot'];

    const CACHE_KEYS = [
        "userApp" => "user-applications",
        "cronApp" => "cronjob-applications"
    ];

    protected static function booted()
    {
        static::created(function(){
            static::clearCache();
        });

        static::updated(function(){
            static::clearCache();
        });

        static::deleted(function(){
            static::clearCache();
        });
    }

    protected static function clearCache()
    {
        foreach(static::CACHE_KEYS as $key){
            \Cache::forget($key);
        }
    }

    // Define a relationship with the Server model
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Define a method to access logs based on server type without storing logs directly in Application.
     * This method will return the corresponding AccessLog model instance based on the server type.
     */
    public function accessLogs()
    {
        $server = $this->server;

        if ($server && $server->web_server == "apache2") {
            return $this->hasMany(ApacheAccessLog::class, 'application_id', 'id');
        } elseif ($server && $server->web_server == "nginx") {
            return $this->hasMany(NginxAccessLog::class, 'application_id', 'id');
        } else {
            return $this->hasMany(OlsAccessLog::class, 'application_id', 'id');
        }
    }

    // Find application accesslog tabel
    public function accessLogTable()
    {
        $server = $this->server;

        if ($server->web_server == "apache2") {
            return "apache_access_logs";
        } elseif ($server->web_server == "nginx") {
            return "nginx_access_logs";
        } else {
            return "ols_access_logs";
        }
    }

    // Relationship: Application has many Bandwidths
    public function bandwidths()
    {
        return $this->hasMany(Bandwidth::class);
    }

    // Relationship: Application has many Bots
    public function bots()
    {
        return $this->hasMany(Bot::class);
    }

    // Relationship: Application has many Browsers
    public function browsers()
    {
        return $this->hasMany(Browser::class);
    }

    // Relationship: Application has many Devices
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    // Relationship: Application has many Ips
    public function ips()
    {
        return $this->hasMany(Ip::class);
    }

    // Relationship: Application has many Methods
    public function methods()
    {
        return $this->hasMany(Method::class);
    }

    // Relationship: Application has many MimeTypes
    public function mimeTypes()
    {
        return $this->hasMany(MimeType::class);
    }

    // Relationship: Application has many Platforms
    public function platforms()
    {
        return $this->hasMany(Platform::class);
    }

    // Relationship: Application has many PlatformVersions
    public function platformVersions()
    {
        return $this->hasMany(PlatformVersion::class);
    }

    // Relationship: Application has many Protocols
    public function protocols()
    {
        return $this->hasMany(Protocol::class);
    }

    // Relationship: Application has many Statuses
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    // Relationship: Application has many urls
    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    // Relationship: Application has many referrers
    public function referrers()
    {
        return $this->hasMany(Referrer::class);
    }

    // Relationship: Application has many throughputs
    public function throughputs()
    {
        return $this->hasMany(Throughput::class);
    }

    // Relationship: Application has many errorRates
    public function errorRates()
    {
        return $this->hasMany(ErrorRate::class);
    }
}
