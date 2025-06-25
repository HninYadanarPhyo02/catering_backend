<?php

namespace App\Http\Controllers;

use App\Models\FoodMenu;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Font;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $menus = FoodMenu::all();
        $menus = FoodMenu::orderBy('created_at', 'desc')->paginate(5);
        $menuCount = FoodMenu::count();
        return view('menus.index', compact('menus','menuCount'));
    }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //     ]);

    //     $existingFood = FoodMenu::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();

    //     if ($existingFood) {
    //         // Redirect back with input and flash error message
    //         return redirect()->back()->with('error', 'This curry is already existed');
    //     }

    //     // Generate new food_id logic here...
    //     $lastMenu = FoodMenu::orderByRaw("CAST(SUBSTRING(food_id, 6) AS UNSIGNED) DESC")->first();
    //     $lastNumber = $lastMenu ? intval(substr($lastMenu->food_id, 5)) : 0;
    //     $newNumber = $lastNumber + 1;
    //     $newFoodId = 'food_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    //     // Create new food menu item
    //     FoodMenu::create([
    //         'food_id' => $newFoodId,
    //         'name' => $request->name,
    //     ]);

    //     return redirect()->route('menus.index')->with('success', "$request->name is added!");
    // }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $nameLower = strtolower($request->name);

        // Search for existing (including soft-deleted) food
        $existingFood = FoodMenu::withTrashed()
            ->whereRaw('LOWER(name) = ?', [$nameLower])
            ->first();

        if ($existingFood) {
            if ($existingFood->trashed()) {
                // Restore the soft-deleted item instead of creating a new one
                $existingFood->restore();

                return redirect()->route('menus.index')->with('success', "{$existingFood->name} has been restored!");
            }

            return redirect()->back()->with('error', 'This curry is already existed');
        }

        // Generate new food_id
        $lastMenu = FoodMenu::orderByRaw("CAST(SUBSTRING(food_id, 6) AS UNSIGNED) DESC")->first();
        $lastNumber = $lastMenu ? intval(substr($lastMenu->food_id, 5)) : 0;
        $newFoodId = 'food_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        FoodMenu::create([
            'food_id' => $newFoodId,
            'name' => $request->name,
        ]);

        return redirect()->route('menus.index')->with('success', "{$request->name} is added!");
    }

    public function edit(FoodMenu $menu)
    {
        $menus = FoodMenu::all();
        return view('menus.index', compact('menus'))->with('editMenu', $menu);
    }

    // public function update(Request $request, FoodMenu $menu)
    // {
    //     $request->validate(['name' => 'required|string|max:255']);
    //     $menu->update($request->only('name'));
    //     $id = $menu->food_id;
    //     return redirect()->route('menus.index')->with('success', "$id is updated!");
    // }
    public function update(Request $request, FoodMenu $menu)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $newName = $request->name;

    // 1. If name is unchanged
    if ($newName === $menu->name) {
        return redirect()->back()->with('info', 'No changes detected. The food name is already the same.');
    }

    // 2. Check if name already exists in another record
    $exists = FoodMenu::where('name', $newName)
        ->where('id', '!=', $menu->id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'This food name is already taken.');
    }

    // 3. Update
    $menu->update(['name' => $newName]);

    return redirect()->route('menus.index')->with('success', "{$menu->food_id} is updated!");
}

    public function destroy(FoodMenu $menu)

    {
        $name = $menu->name;
        $menu->delete();
        return redirect()->route('menus.index')->with('success', "$name is deleted!");
    }
}
