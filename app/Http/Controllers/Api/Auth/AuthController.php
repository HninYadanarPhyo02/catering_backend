<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Imports\EmployeeImport;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'employeeId' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = Employee::where('emp_id', $request->employeeId)->first();

        if (!$user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'isSuceess' => false,
                'message' => 'Invalid credentails'
            ], 401);
        }

        $token = $user->createToken('employee-token')->plainTextToken;

        return response()->json([
            'isSuccess' => true,
            'message' => 'Login Successful',
            'data' => [
                'token' => $token,
                'employee' => [
                    'id' => $user->id,
                    'employeeId' => $user->emp_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ],
        ]);
    }

   }
