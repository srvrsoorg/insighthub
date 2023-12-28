<?php
namespace App\Traits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait DateTime
{
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-m-d H:i:s')
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-m-d H:i:s')
        );
    }
}