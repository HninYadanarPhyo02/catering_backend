<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function edit()
    {
        // Fetch existing settings - example using a model or config
        // Replace this with your actual settings retrieval logic
        $settings = (object)[
            'company_name' => config('catering.company_name', ''),
            'contact_email' => config('catering.contact_email', ''),
            'phone_number' => config('catering.phone_number', ''),
            'address' => config('catering.address', ''),
            'currency' => config('catering.currency', 'USD'),
        ];

        return view('settings', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validate incoming data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
        return back()->with('success', 'Settings updated successfully!');
    }
}
