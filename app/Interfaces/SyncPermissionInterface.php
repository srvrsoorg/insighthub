<?php

namespace App\Interfaces;


interface SyncPermissionInterface 
{
    public function syncAll();
    public function syncPermission($user);
}