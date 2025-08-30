<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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
      if (!auth()->attempt($credentials)) {
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

  // get user function
}
