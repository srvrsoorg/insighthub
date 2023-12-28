<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Database;
use DB;
use Artisan;
use Exception;

class DatabaseController extends Controller
{
    // Retrieve the database configuration
    public function index()
    {
        try {
            // Retrieve the first database configuration and return it as JSON
            return response()->json([
                'database' => Database::first()
            ]);

        } catch (Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Store a new database configuration
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'host' => 'required',
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        try {

            $config = [
                'host' => $request->host,
                'database' => $request->database,
                'username' => $request->username,
                'password' => $request->password,
            ];
            
            // Set database credentials
            $this->setDatabaseCredential($config);

            Artisan::call("cache:clear");
            Artisan::call("config:cache");
            Artisan::call("config:clear");

            // Check Runtime Database Connection
            $this->checkDatabaseConnection($config);

            // Run migration command 
            $this->migrate();

            // Check if database exists 
            if(Database::exists()) {
                Database::first()->delete();
            }
            
            // Create a new Database model instance
            Database::create($config);

            // ✅ Success response: Database configuration saved successfully
            return response()->json([
                'message' => "Database has been configured successfully!"
            ], 200);

        } catch (Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Database Connection Failed. Please verify your database credentials."
            ], 500);
        }
    }

    //Check database connection
    public function checkDatabaseConnection($config) {

        // Establish a connection using the runtime configuration
        DB::connection()->getPdo();
        DB::connection()->getDatabaseName();
    }

    //Set Database Credentials
    public function setDatabaseCredential($config) {

        Artisan::call("env:set db_host {$config['host']}");
        Artisan::call("env:set db_database {$config['database']}");
        Artisan::call("env:set db_username {$config['username']}");
        Artisan::call("env:set db_password {$config['password']}");
    }

    // Database migration
    public function migrate() {
        try {
            DB::beginTransaction();

            Artisan::call("migrate:refresh --force");
            
            $artisanOutput = Artisan::output();

            if (in_array("Error", str_split($artisanOutput, 5))) {
                throw new Exception($artisanOutput);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}