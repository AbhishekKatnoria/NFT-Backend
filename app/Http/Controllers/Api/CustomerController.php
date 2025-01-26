<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Log; 
use App\Helpers\ResponseHelper; 

class CustomerController extends Controller
{
    /**
     * Create a new customer.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function createCustomer(Request $request)
    {
        // Validate incoming request
        $passwordRule = passwordRuleFunc(); 

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email' => ['required', 'string', 'email', 'unique:users', 'regex:/^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)?@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}$/'],
            'password' => ['required', 'confirmed', $passwordRule],
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return ResponseHelper::failResponse($validator->errors(), 422); 
        }

        try {
            // Get the validated data
            $validated = $validator->validated();

            // Create the customer
            $customer = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Return a success response
            return ResponseHelper::successResponse('Account created successfully!', 201);

        } catch (\Exception $e) {
            Log::error('Error creating customer account: ' . $e->getMessage());
            return response('Internal server error', 500);
        }
    }

    /**
     * Display a listing of the customer resource (if needed in the future).
     */
    public function callCustomer(Request $request)
    {
        return $this->createCustomer($request);
    }
}
