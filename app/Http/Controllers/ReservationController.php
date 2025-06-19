<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return view('reservations.index'); // List all reservations
    }

    public function create()
    {
        return view('reservations.create'); // Show form to create a reservation
    }

    public function store(Request $request)
    {
        // Validate and save reservation logic here
        // For now, just redirect back
        return redirect()->route('reservations.index');
    }

    public function show($id)
    {
        return view('reservations.show', compact('id')); // Show single reservation details
    }

    public function edit($id)
    {
        return view('reservations.edit', compact('id')); // Show edit form
    }

    public function update(Request $request, $id)
    {
        // Update reservation logic here
        return redirect()->route('reservations.index');
    }

    public function destroy($id)
    {
        // Delete reservation logic here
        return redirect()->route('reservations.index');
    }
}
