<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class AuthController extends Controller
{
    function getCustomerToken($id){
        $customer = Customer::find($id);

        if (!$customer) {
            throw ValidationException::withMessages([
                'id' => ['Customer not found.'],
            ]);
        }
        
        $deviceName = 'Customer-' . $customer->id; 
    
        return $customer->createToken($deviceName)->plainTextToken;
    }
}