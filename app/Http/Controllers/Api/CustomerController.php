<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


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
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|'.$passwordRule,
        ]);

        try {
       
            // Create the customer 
            $customer = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']), 
            ]);

            // Return a success response
            return successResponse('Account created successfully!',201);

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
