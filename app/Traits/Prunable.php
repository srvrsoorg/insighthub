<?php
namespace App\Traits;

use App\Http\Helper;
use Illuminate\Database\Eloquent\Prunable as Prune;

trait Prunable
{
    use Prune;

    public function prunable()
    {
        $siteSetting = Helper::siteSetting(); // You may need to adjust this based on your Helper class location
        // Check if site settings are available and a retention period is defined
        if ($siteSetting && $days = $siteSetting->retention_period) {
            // Return records that have a 'created_at' date older than the retention period
            return $this->where('created_at', '<=', now()->subDays($days));
        }
    }
}
