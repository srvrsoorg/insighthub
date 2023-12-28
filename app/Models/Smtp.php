<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Smtp extends Model
{
    use HasFactory;

    const CACHE_KEYS = [
        "main" => "smtp-detail"
    ];

    // Define the table name
    protected $table = "smtps";

    // Fields that can be mass-assigned
    protected $fillable = [
        'host',          // SMTP server host
        'port',          // SMTP server port
        'username',      // SMTP username
        'password',      // SMTP password
        'from_email',    // Sender's email address
        'from_name',     // Sender's name
        'encryption'     // SMTP encryption method
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

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

    protected static function clearCache(){
        foreach(static::CACHE_KEYS as $value){
            \Cache::forget($value);
        }
    }
}
