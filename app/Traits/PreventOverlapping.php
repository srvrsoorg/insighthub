<?php

namespace App\Traits;

use Illuminate\Queue\Middleware\WithoutOverlapping;

trait PreventOverlapping {
    public function middleware()
    {
        if(is_object($this->application)){
            return [new WithoutOverlapping($this->application->id)];
        }else{
            return [new WithoutOverlapping($this->application)];
        }
    }
}
