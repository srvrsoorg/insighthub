<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cache;

class UserServer extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'user_servers';

    // Fields that can be mass-assigned
    protected $fillable = [
        'user_id',    // ID of the user associated with the server
        'server_id',  // ID of the server associated with the user
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    // Fields hidden from serialization
    protected $hidden = ['pivot'];

    // Define a relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define a relationship with the Server model
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Define a many-to-many relationship with the Application model
    public function applications()
    {
        return $this->belongsToMany(Application::class);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when UserServer is saved or updated
        static::saved(function () {
            Cache::flush();
        });

        // Clear cache when UserServer is deleted
        static::deleted(function () {
            Cache::flush();
        });
    }
}