<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Update customer information from order form
     */
    public function updateInfo(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::findOrFail($id);
            
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->phone, // Update mobile field with phone value
                'address' => $request->address,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer information updated successfully',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer information: ' . $e->getMessage()
            ], 500);
        }
    }
}
