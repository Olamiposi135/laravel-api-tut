<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{


  public function register(Request $request)
  {
    $fields = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|unique:users',
      'password' => 'required|string|confirmed|min:6'
    ]);

    if ($fields->fails()) {
      return response()->json(['errors' => $fields->errors()], 403);
    }

    try {
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
      ]);

      $token = $user->createToken('auth_token')->plainTextToken;

      // return response
      return response([
        'user' => $user,
        'access_token' => $token
      ], 201); // 201 Created

    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }

  //login function

  public function login(Request $request)
  {
    $validated = Validator::make($request->all(), [

      'email' => 'required|string|email',
      'password' => 'required|string|min:6'
    ]);

    if ($validated->fails()) {
      return response()->json(['errors' => $validated->errors()], 403);
    }

    $credentials = ['email' => $request->email, 'password' => $request->password];

    try {
      if (!Auth::attempt($credentials)) {
        return response()->json(['error' => 'Invalid email or password'], 403);
      }
      $user = User::where('email', $request->email)->firstOrFail();
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'message' => 'Login Successful',
        'access_token' => $token,
        'user' => $user
      ]);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 403);
    }
  }


  // logout function 

  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully']);
  }


  //Forgot and reset password functions

  public function forgotPassword(Request $request)
  {
    $request->validate(['email' => 'required|email']);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return response()->json(['message' => 'We cannot find a user with that email address.'], 404);
    }

    // Delete any existing tokens for this user
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    // Create a new token
    $token = Str::random(60);
    DB::table('password_reset_tokens')->insert([
      'email' => $request->email,
      'token' => Hash::make($token), // Hash the token for storage
      'created_at' => Carbon::now()
    ]);

    // Send email
    Mail::send('emails.password-reset', ['token' => $token, 'email' => $request->email], function (Message $message) use ($request) {
      $message->to($request->email);
      $message->subject('Reset Your Password');
    });

    return response()->json(['message' => 'Password reset link sent to your email.']);
  }

  public function resetPassword(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'token' => 'required',
      'password' => 'required|string|confirmed|min:6',
    ]);

    $passwordReset = DB::table('password_reset_tokens')
      ->where('email', $request->email)
      ->first();

    if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
      return response()->json(['message' => 'This password reset token is invalid or has expired.'], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return response()->json(['message' => 'We cannot find a user with that email address.'], 404);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return response()->json(['message' => 'Password has been reset successfully.']);
  }
}
