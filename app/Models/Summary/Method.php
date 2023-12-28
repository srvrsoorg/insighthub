<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application};
use App\Traits\Prunable;

class Method extends Model
{
    use HasFactory, Prunable;

    // Define the table associated with the Method model in the database
    protected $table = 'methods';

    // Define attributes that are mass assignable
    protected $fillable = [
        'server_id',
        'application_id',
        'type',
        'method',
        'is_bot_request',
        'count',
    ];

    // Relationship to 'Server' model: A Method belongs to a Server
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    // Relationship to 'Application' model: A Method belongs to an Application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}