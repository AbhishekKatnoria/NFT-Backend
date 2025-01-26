<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  protected $fillable = ['name', 'email', 'password'];
  protected $hidden   = ['id','password', 'remember_token', 'created_at', 'updated_at'];


  // Create Super Admin
  protected function createSupAdminModel($request)
  {
    $passwordRule = passwordRuleFunc();

    // Validate the incoming request
    $validator = Validator::make($request->all(), [
      'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u', 'min:3'],
      'email'    => ['required', 'string', 'email', 'unique:users', 'regex:/^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)?@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}$/'],
      'password' => ['required', 'confirmed', $passwordRule]
    ]);

    // Check if validation fails
    if ($validator->fails()) {
      return failResponse($validator->errors(), 422);
    }

    // Check if a super admin already exists
    $existingUser = User::where('role_id', 1)->first();
    if ($existingUser) {
      Log::error('Error creating super admin');
      return failResponse('Bad request', 400);
    }

    DB::beginTransaction();

    try {
      // Create a new user
      $user           = new User();
      $user->name     = $request->input('name');
      $user->email    = $request->input('email');
      $user->password = Hash::make($request->input('password')); // Hash the password
      $user->role_id  = 1;
      $user_create    = $user->save();
      if ($user_create) {
        DB::commit();
        return successResponse('Super admin created successfully', 201);
      } else {
        DB::rollBack();
        return failResponse('Something went wrong.', 400);
      }
    } catch (\Exception $e) {
      // Rollback the DB and log the exception
      DB::rollBack();
      Log::error('Error creating super admin: ' . $e->getMessage());
      return failResponse('Internal server error', 500);
    }
  }

  // Callback for Create Super Admin
  public function callCreateSupAdmin($request)
  {
    return $this->createSupAdminModel($request);
  }

  // Login User
  protected function loginModel($request)
  {
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
      'email'    => ['required', 'string', 'email', 'regex:/^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)?@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}$/'],
      'password' => ['required'],
    ]);

    // Check if validation fails
    if ($validator->fails()) {
      return failResponse($validator->errors(), 422);
    }

    try {
      // Find the user by email
      $user = User::where('email', $request->email)->first();

      if ($user && Hash::check($request->password, $user->password)) {
        // Generate a personal access token
        $token = $user->createToken('Personal login token', ['*'], now()->addMonth())->plainTextToken;
        return response()->json([
        'status'  => true,
        'message' => 'User logged in successfully',
        'token'   => $token,
        ], 200);
       
      } else {
        return failResponse('Invalid credential', 409);
      }
    } catch (\Exception $e) {
      // Log the exception
      Log::error('Error while logging in: ' . $e->getMessage());
      return failResponse('Internal server error', 500);
    }
  }

  // Calling login user
  public function callLogin($request)
  {
    return $this->loginModel($request);
  }

  // Logged In User Profile
  protected function userProfile($request)
  {

    // Profile access
    $user = $request->get('tokenableUser');
    $currentUser = $user;
    try {
      if ($currentUser) {
        return successResponse('Current user details', 200, $currentUser);
      } else {
        return failResponse('User not found', 404);
      }
    } catch (\Exception $e) {
      Log::error('Error while fetching the profile data: ' . $e->getMessage());
      return failResponse('Internal server error.', 500);
    }
  }

  public function showUserProfile($request)
  {
    return $this->userProfile($request);
  }

  // User Logout
  protected function userLogout()
  {
    $isLogOut = auth()->user()->tokens()->delete();
    try {
      if ($isLogOut) {
        return successResponse('User logged out successfully', 200);
      } else {
        return failResponse('Failed to logout the current user', 400);
      }
    } catch (\Exception $e) {
      Log::error('Error while logging out: ' . $e->getMessage());
      return failResponse('Internal server error.', 500);
    }
  }

  public function userLoggedOut()
  {
    return $this->userLogout();
  }
}