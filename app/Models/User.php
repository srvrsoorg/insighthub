<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Helper;
use App\Traits\DateTime;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, DateTime;

    // Define the table name
    protected $table = 'users';

    // Fields that can be mass-assigned
    protected $fillable = [
        'name',         // User's name
        'email',        // User's email address
        'password',     // User's password
        'role',         // User's role (e.g., 'administrator')
        'designation',  // User's designation
        'api_token',     // User's API token,
        'avatar'        // User's avatar
    ];

    // Fields hidden from serialization
    protected $hidden = [
        'role',
        'api_token',
        'password',
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    // Get the host attribute from the HTTP request
    public function getHostAttribute()
    {
        return request()->getHttpHost();
    }

    // Create an authentication API token for the user
    public function createApiToken()
    {
        $token = Helper::generateUniqueToken(64, 'users', 'api_token');
        $this->api_token = $token;
        $this->save();
        return $token;
    }

    // Check if the user is an administrator
    public function isSuperAdmin()
    {
        return $this->role == 'administrator';
    }

    // Define a relationship with UserServer model
    public function userServers()
    {
        return $this->hasMany(UserServer::class);
    }
}