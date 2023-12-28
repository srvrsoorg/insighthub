<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\ApacheAccessLog;
use App\Models\Application;
use App\Models\NginxAccessLog;
use App\Models\OlsAccessLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(Application::whereHas('server', function ($query) { $query->where('web_server', 'apache2'); })->exists()){
            ApacheAccessLog::factory(10)->create();
        }

        if(Application::whereHas('server', function ($query) { $query->where('web_server', 'nginx'); })->exists()){
            NginxAccessLog::factory(10)->create();
        }

        if(Application::whereHas('server', function ($query) { $query->where('web_server', 'ols'); })->exists()){
            OlsAccessLog::factory(10)->create();
        }
    }
}
