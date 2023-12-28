<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, BasicDetail};
use App\Http\Helper;
use App\Interfaces\SyncPermissionInterface;

class InstallationController extends Controller
{
    private SyncPermissionInterface $syncPermissionInterface;

    public function __construct(SyncPermissionInterface $syncPermissionInterface) 
    {
        $this->syncPermissionInterface = $syncPermissionInterface;
    }

    // User registration
    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            // Check if an administrator user already exists
            if (User::where('role', 'administrator')->exists()) {
                // ❌ Error response: Only one user is designated as an admin
                return response()->json([
                    "message" => "Only one user is designated as an admin."
                ], 500);
            }

            // Create a new user with admin privileges
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'designation' => "admin",
                'role' => "administrator",
                'avatar' => Helper::gravatar($request->email)
            ]);

            // Sync all servers and applications
            $this->syncPermissionInterface->syncAll();

            // Sync all permissions
            $this->syncPermissionInterface->syncPermission($user);

            // Generate and save an API token for the new user
            $token = $user->createApiToken();

            // ✅ Success response: Registration successful
            return response()->json([
                'user' => $user,
                'is_admin' => $user->isSuperAdmin(),
                'token' => "Bearer ".$token,
                'message' => 'Registration successful.'
            ], 200);

        } catch (\Exception $e) {
            if($user) {
                $user->delete();
            }
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => $e->getMessage() ? $e->getMessage() : "Something went wrong."
            ], 500);
        }
    }

    public function checkPermission(Request $request)
    {
        try {
            
            // ✅ Success response: Return the permissions for each folder
            return response()->json([
                'permission' => Helper::verifyStoragePermission()
            ]);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    // Verify domain and license key
    public function verify(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'domain' => 'required',
            'license_key' => 'required'
        ]);

        try {

            // Update or create the "license key" basic detail
            BasicDetail::updateOrCreate(['key' => 'license_key'], ['value' => $request->license_key]);

            // Prepare request data
            $requestData = [
                'domain' => $request->domain,
                'license_key' => $request->license_key
            ];

            // Make an external request to verify domain and license key
            $response = Helper::serveravatarClient("monitoring-panel/verify", 'post', $requestData);

            if (isset($response['error'])) {
                BasicDetail::where('key', 'license_key')->delete();
                // ❌ Error response
                return response()->json([
                    'message' => $response['message']
                ], 500);
            }

            \Artisan::call("env:set app_url https://{$request->domain}");

            // ✅ Success response: Domain and license key verified
            return response()->json([
                'message' => $response['message']
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}