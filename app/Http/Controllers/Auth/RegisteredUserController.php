<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\RegisteredOrder;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // dd($request->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $lastEmployee = Employee::orderBy('emp_id', 'desc')->first();

        // If no employees yet, start from 1
        $lastNumber = $lastEmployee ? intval(substr($lastEmployee->emp_id, 4)) : 0;
        $newNumber = $lastNumber + 1;
        $newEmpId = 'emp_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        $user = Employee::create([
            'id' => (string) Str::uuid(),
            'emp_id' => $newEmpId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'employee',
        ]);
        // dd($user);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
    public function list()
    {
        $data = Employee::get();
        if ($data) {
            $data = RegisteredOrder::collection($data);
        }
        return response([
            'isSuccess' => true,
            'message' => 'Success',
            'data' => $data,
        ], 200);
    }
}
