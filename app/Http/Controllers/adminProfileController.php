<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class adminProfileController extends Controller
{
    public function index()
    {
        $admin = Auth::user(); // Assuming admin is authenticated using Laravel's Auth

        return view('admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employee,email,' . $admin->emp_id . ',emp_id',
            'old_password' => ['nullable', 'current_password'],
            'new_password' => ['nullable', 'string', Password::min(8), 'confirmed'],
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('old_password') && $request->filled('new_password')) {
            $admin->password = Hash::make($request->new_password);
        }

        $admin->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
