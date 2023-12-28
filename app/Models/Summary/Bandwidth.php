<?php

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Server, Application}; // Importing Server and Application models
use App\Traits\Prunable; // Importing Prunable trait

class Bandwidth extends Model
{
    use HasFactory, Prunable; // Using HasFactory and Prunable traits

    protected $table = 'bandwidths'; // Table name for bandwidth records

    protected $fillable = [ // Fields that can be mass-assigned
        'server_id',
        'application_id',
        'type',
        'url',
        'mime_type',
        'document_type',
        'bandwidth',
        'is_bot_request',
        'count'
    ];

    // Relationship to 'Server' model
    public function server()
    {
        return $this->belongsTo(Server::class); // Relationship to the Server model
    }

    // Relationship to 'Application' model
    public function application()
    {
        return $this->belongsTo(Application::class); // Relationship to the Application model
    }
}
