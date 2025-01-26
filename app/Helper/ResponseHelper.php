<?php
// Helper File

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;


// Fetch user details based on token
function userDetails($request)
{
  $user = $request->attributes->get('tokenableUser');
  return $user;
}

// Failure Response function
function failResponse($errors, $statusCode, $details = '')
{
  return response()->json([
    'status'  => false,
    'errors' => $errors,
    'details' => $details,
  ], $statusCode);
}

// Success Response function
function successResponse($message, $statusCode, $data = '')
{
  return response()->json([
    'status'  => true,
    'message' => $message,
    'data'    => $data,
  ], $statusCode);
}

// Define the password rule
function passwordRuleFunc()
{
  $appEnv       = env('APP_ENV');
  $passwordRule = Password::min(8);

  if ($appEnv != 'local') {
    $passwordRule = Password::min(8)
      ->letters()
      ->mixedCase()
      ->numbers()
      ->symbols()
      ->uncompromised();
  }

  return $passwordRule;
}

