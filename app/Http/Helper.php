<?php

namespace App\Http;
use DB;
use App\Models\{BasicDetail, SiteSetting, Application};
use Illuminate\Support\Facades\File;
use \Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use Cache;

class Helper
{	

	// Generate user avatar
	public static function gravatar(string $email, int $size = 200)
	{
		try {
			$gravatarUrl = "https://www.gravatar.com/avatar/".md5( strtolower(trim( $email )))."?d=" . urlencode( "" )."&s={$size}";
		} catch(\Exception $e) {
			$gravatarUrl = "https://www.gravatar.com/avatar/cb8419c1d471d55fbca0d63d1fb2b6ac?d=&s={$size}";
		}
		return $gravatarUrl;
	}

    // Generate a unique token
    public static function generateUniqueToken(int $size = 10, string $table = null, string $column = null)
	{
		$token = Str::random($size);
		if($table && DB::table($table)->where($column,$token)->count()){
			self::generateUniqueToken($size, $table, $column);
		}

		return $token;
	}

    // Make a request to the Serveravatar API
	public static function serveravatarClient($endpoint, $method, $requestData = null)
	{
	    try {
	        // Get the Serveravatar API key from the database
	        $licenseKey = BasicDetail::where('key', 'license_key')->first();
	        
	        if (!$licenseKey) {
	            // Return an error response if the API key is not found
	            return ["error" => true, "message" => "Please set license key."];
	        }

	        // Initialize GuzzleHttp client with headers
	        $client = new \GuzzleHttp\Client([
	            'headers' => [
	                'Authorization' => $licenseKey->value,
	                'content-type' => 'application/json',
	                'accept' => 'application/x-www-form-urlencoded',
	                'User-Agent' => 'Insighthub'
	            ]
	        ]);

	        // Make a request to the Serveravatar API endpoint
	        $response = $client->request($method, config('app.serveravatar') . "/" . $endpoint, [
	            'connect_timeout' => 5000,
	            'form_params' => $requestData,
	        ]);
	        
		    return json_decode($response->getBody(), true);
		    
	    } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
            $responseBody = $response->getBody()->getContents();

            $errorResponse = json_decode($responseBody, true);

            // Extract the error message
            $errorMessage = isset($errorResponse['message']) ? $errorResponse['message'] : 'Unknown server error.';

            // Handle the error message as needed, and return it with the same status code
            return ["error" => true, 'message' => $errorMessage];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
			$exception = (string)$e->getResponse()->getBody();
			$exception = json_decode($exception);
	        return ["error" => true, "message" => isset($exception->message) ? $exception->message : "Something went wrong."];
		} catch (\Exception $e) {
			report($e->getMessage());
	        // Handle other exceptions
	        return ["error" => true, "message" => $e->getMessage()??"Something went wrong."];
	    }
	}

	public static function siteSetting() {

		try {

			// Retrieve the site setting
	        $siteSetting = SiteSetting::first();
	        if($siteSetting) {
	            // Construct URLs for favicon, logo, and icon images
	            $favicon = null;
	            if($siteSetting->favicon) {
	                $favicon = url('/storage/favicon/'.$siteSetting->favicon);
	            }

	            $logo = null;
	            if($siteSetting->logo){
	                $logo = url('/storage/logo/'.$siteSetting->logo);
	            }

	            $icon = null;
	            if($siteSetting->icon){
	                $icon = url('/storage/icon/'.$siteSetting->icon);
	            }

	            // Update site setting with image URLs

	            $siteSetting->favicon = $favicon;
	            $siteSetting->logo = $logo;
	            $siteSetting->icon = $icon;
	            $siteSetting->redis_password = config('database.redis.default.password');
	        }

	        // ✅ Success response: Return the site setting
            return $siteSetting;

		} catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            return null;
        }
	}

    // Check permission
    public static function verifyStoragePermission()
    {
    	try {
	    	// Define the paths to the storage directories
	        $appFolder = storage_path('app');
	        $frameworkFolder = storage_path('framework');
	        $logFolder = storage_path('logs');
	        $cacheFolder = storage_path('framework/cache');

	        // Initialize variables to track folder write and read permissions
	        $isAppFolderWriteable = false;
	        $isFrameworkFolderWriteable = false;
	        $isLogFolderWriteable = false;
	        $isCacheFolderWriteable = false;

	        // Check if the 'app' folder is both writable and readable
	        if (File::isWritable($appFolder) && File::isReadable($appFolder)) {
	            $isAppFolderWriteable = true;
	        }

	        // Check if the 'framework' folder is both writable and readable
	        if (File::isWritable($frameworkFolder) && File::isReadable($frameworkFolder)) {
	            $isFrameworkFolderWriteable = true;
	        }

	        // Check if the 'logs' folder is both writable and readable
	        if (File::isWritable($logFolder) && File::isReadable($logFolder)) {
	            $isLogFolderWriteable = true;
	        }

	        // Check if the 'framework/cache' folder is both writable and readable
	        if (File::isWritable($cacheFolder) && File::isReadable($cacheFolder)) {
	            $isCacheFolderWriteable = true;
	        }

	        // ✅ Success response: Return the permissions for each folder
	        return [
	            'app' => $isAppFolderWriteable,
	            'framework' => $isFrameworkFolderWriteable,
	            'log' => $isLogFolderWriteable,
	            'cache' => $isCacheFolderWriteable,
	        ];
    	} catch(\Exception $e) {
    		return [
	            'app' => false,
	            'framework' => false,
	            'log' => false,
	            'cache' => false,
	        ];
    	}
    }

    // return time for given range
    public static function getDateRange()
    {
    	if(request()->has('start_date') && request()->has('end_date')) {
    		$startDate = Carbon::parse(request()->get('start_date'))->format("Y-m-d H:i:s");
    		$endDate = Carbon::parse(request()->get('end_date'))->format("Y-m-d H:i:s");
        	return [$startDate, $endDate];
    	} else {
    		return [today()->subDays(7)->format('Y-m-d 00:00:00'), today()->format('Y-m-d 23:59:59')];
    	}
    }

    // Function to retrieve user's server and application IDs based on user permissions
	public static function permissionIds()
	{
	    try {
	        $user = auth()->user(); // Retrieve the authenticated user

	        $serverId = request('server_id'); // Get the server ID from the request
	        $applicationId = request('application_id'); // Get the application ID from the request

            $cacheKey = self::generateCacheKey($user->id, $serverId, $applicationId);

            return Cache::rememberForever($cacheKey, function () use ($user, $serverId, $applicationId) {

		        // Query to fetch user servers
		        $userServersQuery = $user->userServers();

		        // Fetch server IDs based on user permissions
		        $serverIds = $userServersQuery->when($serverId, function ($query) use ($serverId) {
		            $query->where('server_id', $serverId); // Apply server ID filter if provided
		        })->pluck('server_id')->all(); // Retrieve server IDs

		        // Fetch application IDs based on server IDs and user permissions
		        $applicationIds = $user->userServers()
		            ->whereIn('server_id', $serverIds)
		            ->with(['applications' => function ($query) use ($applicationId) {
		                $query->when($applicationId, function ($query) use ($applicationId) {
		                    $query->where('applications.id', $applicationId); // Apply application ID filter if provided
		                });
		            }])
		            ->get()
		            ->flatMap->applications->pluck('id')->unique()->values()->all(); // Retrieve unique application IDs

		        // Return server IDs and application IDs
		        return compact('serverIds', 'applicationIds');
		    });

	    } catch (\Exception $e) {
	        return ['serverIds' => [], 'applicationIds' => []]; // Return empty arrays in case of an exception
	    }
	}

	// Generate cache key
	public static function generateCacheKey($userId, $serverId, $applicationId)
    {
        return 'user_permissions_' . $userId . '_server_' . $serverId . '_app_' . $applicationId;
    }
	
	// Check robots txt
	public static function isRobotsTxt($url = null) {
		try {
			return Str::contains($url, 'robots.txt');
		} catch(\Exception $e) {
			return false;
		}
	}

	// Check sitemap url
	public static function isSitemapUrl($url = null) {
		try {
			return Str::contains($url, 'sitemap.xml');
		} catch(\Exception $e) {
			return false;
		}
	}

	// Check xmlrpc request
	public static function isXmlrpcRequest($url = null) {
		try {
			return Str::contains($url, 'xmlrpc');
		} catch(\Exception $e) {
			return false;
		}
	}

	// Get referrer domain
	public static function getReferrerDomain($url) {
		try {
			$sourceUrl = parse_url($url);
			return $sourceUrl['host'];
		} catch(\Exception $e) {
			return null;
		}
	}

	// Return per page
	public static function perPage() {
		try {
			return request('per_page') ? request('per_page') : 5;
		} catch(\Exception $e) {
			return 5;
		}
	}

    // Helper function to get all dates within a date range
    public static function getDatesInRange($startDate, $endDate)
    {
        $dates = [];
        $currentDate = new \DateTime($startDate);
        while ($currentDate <= new \DateTime($endDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->add(new \DateInterval('P1D'));
        }
        return $dates;
    }

    // Get title from url
    public static function getTitleFromUrl(Application $application, $url) {
    	try {
    		$domain = $application->primary_domain;

    		$fullurl = "$domain$url";

    		if($title = $application->urls()->select('url','title')->where('url',$url)->whereNotNull('title')->first()) {
    			return $title;
    		} else {

	    		// Create a Guzzle client with options to allow redirects and ignore SSL certificate errors
		        $client = new \GuzzleHttp\Client([
		            'allow_redirects' => true,
		            'verify' => false, // Set to false to bypass SSL certificate errors,
					'timeout' => 5,
					'headers' => [
						'User-Agent' => 'Insighthub-ServerAvatar'
					]
		        ]);

		        $response = $client->get($fullurl);
	            $html = $response->getBody()->getContents();

	            $crawler = new Crawler($html);

	            // Extract title tag text
	            $title = $crawler->filter('title')->text();

	            return $title;
    		}
    	} catch(\Exception $e) {
    		return null;
    	}
    }
}