<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Rules\PasswordComplexity;  // Import the custom password rule
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{
    /**
     * Register API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // Validate the incoming data with custom password complexity rule
        $validator = Validator::make($request->all(), [
            'customer_name'       => 'required|string|max:255',
            'customer_email'      => 'required|email|unique:users,email|max:255',
            'customer_password'   => ['required', 'string', 'confirmed', new PasswordComplexity], // Use custom rule
            'c_password' => 'required|same:customer_password',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 409);       
        }

        // Create a new user
        $input = $request->all();
        $input['customer_password'] = bcrypt($input['customer_password']); // Hash the password
        $user = User::create($input); // Create user in the database

        // Return success response
        return $this->sendResponse('User registered successfully.');
    }

    /**
     * Login API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {

        // Validate request
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email',
            'customer_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        
    }
}
