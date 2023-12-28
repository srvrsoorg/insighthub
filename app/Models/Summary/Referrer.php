<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Prunable;

class Referrer extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the Referrer model in the database
    protected $table = 'referrers';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'referrer_url',
        'referrer_domain',
        'bandwidth',
        'is_bot_request',
        'count',
    ];

    // Relationship to 'Server' model: A Referrer belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Relationship to 'Application' model: A Referrer belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}