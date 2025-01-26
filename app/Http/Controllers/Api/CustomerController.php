<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Log; 
use App\Helper\ResponseHelper; 

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
            return failResponse($validator->errors(), 422); 
        }

        try {

            // Create the customer
            $customer = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            // Return a success response
            if($customer) {
                return successResponse('Account created successfully!', 201);
            }else{
                return failResponse('Internal server error', 500);
            }

        } catch (\Exception $e) {
            Log::error('Error creating customer account: ' . $e->getMessage());
            return failResponse('Internal server error', 500);
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
