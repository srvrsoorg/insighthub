<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application};
use App\Traits\Prunable;

class Status extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the Status model in the database
    protected $table = 'statuses';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'status',
        'is_bot_request',
        'count',
    ];

    // Relationship to 'Server' model: A Status belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Relationship to 'Application' model with default value: A Status belongs to an Application (with default value)
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}