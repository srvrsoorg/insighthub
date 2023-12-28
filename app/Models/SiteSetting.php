<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'site_settings';

    // Fields that can be mass-assigned
    protected $fillable = [
        'app_name',   
        'favicon',
        'logo',
        'icon',
        'color_code',
        'color_palette',
        'retention_period'        
    ];

    // Dates that should be treated as date objects
    protected $dates = ['created_at', 'updated_at'];

    // Define the casting of field as an array
    protected $casts = [
        'color_palette' => 'array',
    ];
}
