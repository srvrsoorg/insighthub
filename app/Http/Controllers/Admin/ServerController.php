<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{BasicDetail, Server, Application, User, UserServer};
use App\Http\Helper;
use App\Interfaces\SyncPermissionInterface;

class ServerController extends Controller
{
    private SyncPermissionInterface $syncPermissionInterface;

    public function __construct(SyncPermissionInterface $syncPermissionInterface) 
    {
        $this->syncPermissionInterface = $syncPermissionInterface;
    }

    // Retrieve and return a list of servers
    public function index()
    {
        try {
            
            // Search/Paginate with return the list of servers
            $servers = Server::query()
                ->orderBy('created_at','desc')
                ->when(request('search'), function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%')
                            ->orWhere('ip', 'like', '%' . request('search') . '%');
                })
                ->when(request()->has('pagination'), fn ($query) => $query->paginate(10), fn ($query) => $query->get());

            return response()->json([
                'servers' => $servers
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    // Sync all server with application
    public function syncAll()
    {
        try {

            $user = auth()->user();

            // Sync all servers and applications
            $this->syncPermissionInterface->syncAll();

            // Attach admin permission
            if(User::where('role', 'administrator')->exists()) {
                foreach(User::where('role', 'administrator')->get() as $admin) {
                    $this->syncPermissionInterface->syncPermission($admin);
                }
            }

            return response()->json([
                "message" => "All servers and applications have been synchronized successfully."
            ],200);

        } catch (\Exception $e) {
            report($e);
            // ❌ Error response: Handle and respond to any exceptions
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}