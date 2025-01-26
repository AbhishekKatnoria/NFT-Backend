<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
  
  /**
   *
   * Login
   * 
   * Fields:
   * - `email`: string, required
   * - `password`: string, required
   * 
   */
  public function loginUser(Request $request)
  {
    $userlogin = new User();
    $response   = $userlogin->callLogin($request);
    return $response;
  }

  /**
   * Profile
   * 
   * Current logged-in user's profile route.
   */
  public function userProfileRoute(Request $request)
  {
    $profile = new User();
    $response   = $profile->showUserProfile($request);
    return $response;
  }

   /**
   * Logout
   * 
   * Logout route for the current logged-in user.
   */
  public function userLogoutRoute()
  {
    $profile = new User();
    $response   = $profile->userLoggedOut();
    return $response;
  }
}