<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Smtp, SiteSetting};

class SmtpController extends Controller
{
    // Store or update SMTP configuration
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|numeric',
            'username' => 'required|string',
            'password' => 'required|string',
            'from_name' => 'required|string',
            'from_email' => 'required|email',
            'encryption' => 'required|string'
        ]);

        try {

            if(SiteSetting::exists() && !auth()->user()) {
                return response()->json([
                    'message' => "You cannot set up the smtp after a full setup.",
                ], 500);
            }

            // Check if SMTP configuration exists
            if(Smtp::exists()) {
                Smtp::first()->delete();
            }
            
            // Create a new Smtp model instance
            Smtp::create($request->post());            

            // Restart the queue worker for changes to take effect
            \Artisan::call('queue:restart');

            // ✅ Success response: SMTP configuration has been created/updated successfully
            return response()->json([
                'message' => "SMTP configuration has been created successfully!"
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Retrieve and return SMTP configuration
    public function index()
    {
        try {
            // Return SMTP configuration as JSON
            return response()->json([
                'smtp' => Smtp::first()
            ]);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    // Send a test email
    public function testMail()
    {
        try {
            $user = auth()->user();

            // Check if SMTP configuration exists
            if (!Smtp::exists()) {
                return response()->json([
                    'message' => "SMTP details not found."
                ], 500);
            }

            // Send a test email
            \Mail::to($user->email)->send(new \App\Mail\User\SendTestMail($user));

            // ✅ Success response: Test mail sent successfully
            return response()->json([
                'message' => "Test mail sent successfully."
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }
}