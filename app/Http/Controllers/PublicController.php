<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Http\Helper;
use App\Models\{User, Smtp, Database, SiteSetting, BasicDetail};

class PublicController extends Controller
{
    /**
     * Handle user login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        // Request validation
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        try {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 

                $user = auth()->user();

                $token = $user->createApiToken(); # Generate token

                return response()->json([
                    'user' => $user,
                    'is_admin' => $user->isSuperAdmin(),
                    'message' => 'You have successfully logged in.',
                    'token' => "Bearer ".$token
                ], 200);
            } else { 
                return response()->json(['message'=>'Invalid username or password.'], 500);
            }             
        } catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Send a password reset link to the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        // Request Validations
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            if (!Smtp::exists()) {
                return response()->json([
                    "message" => "Smtp configuration not found."
                ], 500);
            }

            // Check if the user exists
            if (!User::where('email', $request->email)->exists()) {
                // Error response
                return response()->json([
                    'message' => "The provided email is not registered."
                ], 500);
            }

            // Find user email
            if ($user = User::where('email', $request->email)->first()) {
                if (!DB::table('password_resets')->where('email', $user->email)->exists()) {
                    $token = Helper::generateUniqueToken(32,'password_resets','token');

                    // Insert email and token in password resets
                    DB::table('password_resets')->insert([
                        'email' => $user->email,
                        'token' => $token,
                        'created_at' => now()
                    ]);
                } else {
                    // Token found
                    $token = DB::table('password_resets')->where('email', $user->email)->first()->token;
                }
                
                // Send a password reset link to the user via email
                \Mail::to($user->email)->send(new \App\Mail\User\PasswordResetLink($user, $token));
            }

            // Success response
            return response()->json([
                'message' => "You will shortly receive a password reset link."
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Reset the user's password using a password reset token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        if (!$token = DB::table('password_resets')->where('token', $request->token)->first()) {
            // Not found response
            return response()->json([
                'message' => "Password reset token is invalid!"
            ], 404);
        }

        $user = User::where('email', $token->email)->first();
      
        // Request Validations
        $request->validate([
            'password' => ["required", "confirmed", "min:8"]
        ]);

        try {
            $user->password = bcrypt($request->password);
            $user->save();

            // Revoke all tokens
            foreach ($user->tokens as $token) {
                $token->revoke();
            }

            // Delete the token
            DB::table('password_resets')->where('token', $request->token)->delete();

            // Success response
            return response()->json([
                'message' => "Your password has been updated successfully!"
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Retrieve the installation steps status.
     *
     * @return array
     */
    public function installationSteps() {
        try {
            $setup['database'] = Database::exists();
            $setup['smtp'] = Smtp::exists();
            $setup['site_setting'] = SiteSetting::exists();
            $setup['license_key'] = BasicDetail::where('key', 'license_key')->exists();
            $setup['register'] = User::exists();

            //Check storage permission
            $setup['storage_permission'] = $this->checkStoragePermisison();

            return $setup;

        } catch (\Exception $e) {
            $setup['database'] = false;
            $setup['smtp'] = false;
            $setup['site_setting'] = false;
            $setup['license_key'] = false;
            $setup['register'] = false;
            
            //Check storage permission
            $setup['storage_permission'] = $this->checkStoragePermisison();

            return $setup;
        }

    }

    // check storage permission
    public function checkStoragePermisison()
    {
        //Check storage permission
        $permission = Helper::verifyStoragePermission();

        $result = array_reduce(array_values($permission), function ($carry, $value) {
            return $carry && $value;
        }, true);

        return $result;
    }
}