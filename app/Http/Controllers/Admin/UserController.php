<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Server, UserServer, Application};
use App\Http\Controllers\ServerController;
use App\Http\Helper;
use App\Rules\{ServerExist,UpdatedServerExist};
use App\Interfaces\SyncPermissionInterface;

class UserController extends Controller
{
    private SyncPermissionInterface $syncPermissionInterface;

    public function __construct(SyncPermissionInterface $syncPermissionInterface) 
    {
        $this->syncPermissionInterface = $syncPermissionInterface;
    }

    // Retrieve and paginate users with their associated servers and applications
    public function index()
    {
        try {
            $users = User::select("id", "email", "role", "name", "designation", "created_at")
                ->when(request('search'), function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%');
                })
                ->paginate(10);

            $users->map(function($user) {
                $user->is_admin = $user->isSuperAdmin();
                unset($user->role);
                return $user;
            });

            // ✅ Success response: Return paginated users with their associated data
            return response()->json([
                'users' => $users
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Create a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'designation' => 'required'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'designation' => $request->designation,
                'avatar' => Helper::gravatar($request->email),
            ]);

            // ✅ Success response: User created successfully
            return response()->json([
                'message' => 'User created successfully.'
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    // Update user details
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'designation' => 'required',
            'password' => 'nullable|min:8',
        ]);

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->designation = $request->designation;
            $user->avatar = Helper::gravatar($request->email);

            if($request->password){
                $user->password = bcrypt($request->password);
            }
                
            $user->save();

            // ✅ Success response: User detail updated successfully
            return response()->json([
                "message" => "User detail updated successfully."
            ]);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // get users all permisison
    public function permissions(User $user)
    {
        try {

            $users = UserServer::where('user_id',$user->id)->select('id','server_id')->paginate(10);

            $users->map(function($user) {
                $user->server = $user->server()->select('id','name')->first();
                $user->applications = $user->applications()->where('server_id',$user->server_id)->select('id','name')->get();
                unset($user->server_id);
                return $user;
            });

            // ✅ Success response: Return paginated users with their associated data
            return response()->json([
                'permissions' => $users,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Attach permissions to a user (server and applications)
    public function attachPermission(Request $request, User $user)
    {
        $request->validate([
            'server_id' => ['required', new ServerExist($user)],
            'application_ids' => 'required|array'
        ], [
            'server_id.required' => "Please select at least one server.",
            'application_ids.required' => "Please select at least one application."
        ]);

        try {
            // Permission attach
            $server = Server::where('id', $request->post('server_id'))->first();

            if(!$server) {
                // ❌ not found response
                return response()->json([
                    'message' => "Server not found!"
                ], 404);
            }

            $userServer = UserServer::updateOrCreate([
                'user_id' => $user->id,
                'server_id' => $server->id
            ]);

            $applications = Application::whereIn('id', $request->post('application_ids'))->get();
            $userServer->applications()->detach($applications);
            $userServer->applications()->attach($applications);

            // ✅ Success response: User permission attached successfully
            return response()->json([
                'message' => "User permission attached successfully."
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Update permissions for a user (server and applications)
    public function updatePermission(Request $request, User $user)
    {
        $userServer = $user->userServers()->find($request->user_server_id);

        $request->validate([
            'user_server_id' => 'required',
            'server_id' => ['required', new UpdatedServerExist($user, $userServer)],
            'application_ids' => 'required|array'
        ], [
            'server_id.required' => "Please select at least one server.",
            'application_ids.required' => "Please select at least one application."
        ]);

        try {
            $userServer = $user->userServers()->find($request->user_server_id);

            if (!$userServer) {
                // ❌ Error response: User server permission not found
                return response()->json([
                    'message' => "User server permission not found."
                ], 404);
            }

            // Permission attach
            $server = Server::where('id', $request->post('server_id'))->first();
            $userServer->update(['server_id' => $server->id]);

            $applications = Application::where('server_id', $server->id)->whereIn('id', $request->post('application_ids'))->get();
            if (empty($applications)) {
                // No applications found in the server
                return response()->json([
                    "message" => "Application not found in server."
                ], 404);
            }

            $userServer->applications()->detach($userServer->applications);
            $userServer->applications()->attach($applications);

            // ✅ Success response: User permission updated successfully
            return response()->json([
                'message' => "User permission updated successfully."
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Delete permissions for a user (server and applications)
    public function deletePermission(Request $request, User $user, $permission)
    {
        try {
            $userServer = $user->userServers()->find($permission);

            if (!$userServer) {
                // ❌ Error response: User server permission not found
                return response()->json([
                    'message' => "User server permission not found."
                ], 404);
            }
            //detach all application permission
            $userServer->applications()->detach($userServer->applications);

            $userServer->delete();

            // ✅ Success response: User permission removed successfully
            return response()->json([
                'message' => "User permission removed successfully."
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Delete permissions for a user (server and applications)
    public function destory(Request $request, User $user)
    {
        try {
            if($user->isSuperAdmin()) {
                // ❌ Not perform
                return response()->json([
                    'message' => "You cannot delete the admin user."
                ], 500);
            }

            $user->delete();

            // ✅ Success response: User removed successfully
            return response()->json([
                'message' => "User removed successfully."
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    public function syncAllPermission(User $user)
    {
        try {

            // Sync all servers and applications
            $this->syncPermissionInterface->syncAll();

            // Sync all permissions
            return response()->json($this->syncPermissionInterface->syncPermission($user));

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}