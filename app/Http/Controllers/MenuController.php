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
    // public function index()
    // {
    //     // $menus = FoodMenu::all();
    //     $menus = FoodMenu::orderBy('created_at', 'desc')->paginate(5);
    //     $menuCount = FoodMenu::count();
    //     return view('menus.index', compact('menus', 'menuCount'));
    // }
    public function index(Request $request)
{
    $search = $request->input('search');

    $query = FoodMenu::orderBy('created_at', 'desc');

    if ($search) {
        $query->where('name', 'like', '%' . $search . '%');
    }

    $menus = $query->paginate(10);
    $menuCount = $query->count();

    if ($request->ajax()) {
        $view = view('menus.partials.menu_table_rows', compact('menus'))->render();
        return response()->json(['html' => $view]);
    }

    return view('menus.index', compact('menus', 'menuCount'));
}


public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $nameLower = strtolower($request->name);

    $existingFood = FoodMenu::withTrashed()
        ->whereRaw('LOWER(name) = ?', [$nameLower])
        ->first();

    if ($existingFood) {
        if ($existingFood->trashed()) {
            $existingFood->restore();

            if ($request->ajax()) {
                return response()->json(['success' => "{$existingFood->name} has been restored!"]);
            }

            return redirect()->route('menus.index')->with('success', "{$existingFood->name} has been restored!");
        }

        if ($request->ajax()) {
            return response()->json(['error' => 'Food name is already defined.']);
        }

        return redirect()->back()->with('error', 'This curry is already existed');
    }

    $lastMenu = FoodMenu::orderByRaw("CAST(SUBSTRING(food_id, 6) AS UNSIGNED) DESC")->first();
    $lastNumber = $lastMenu ? intval(substr($lastMenu->food_id, 5)) : 0;
    $newFoodId = 'food_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

    $menu = FoodMenu::create([
        'food_id' => $newFoodId,
        'name' => $request->name,
    ]);

    if ($request->ajax()) {
        return response()->json([
            'success' => "{$request->name} is added!",
            'newMenu' => [
                'id' => $menu->id,
                'name' => $menu->name,
            ],
        ]);
    }

    return redirect()->route('menus.index')->with('success', "{$request->name} is added!");
}
    public function edit(FoodMenu $menu)
    {
        $menus = FoodMenu::all();
        return view('menus.index', compact('menus'))->with('editMenu', $menu);
    }

    //     public function update(Request $request, FoodMenu $menu)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //     ]);

    //     $newName = $request->name;

    //     // 1. If name is unchanged
    //     if ($newName === $menu->name) {
    //         return redirect()->back()->with('info', 'No changes detected. The food name is already the same.');
    //     }

    //     // 2. Check if name already exists in another record
    //     $exists = FoodMenu::where('name', $newName)
    //         ->where('id', '!=', $menu->id)
    //         ->exists();

    //     if ($exists) {
    //         return redirect()->back()->with('error', 'This food name is already taken.');
    //     }

    //     // 3. Update
    //     $menu->update(['name' => $newName]);

    //     return redirect()->route('menus.index')->with('success', "{$menu->food_id} is updated!");
    // }
    public function update(Request $request, FoodMenu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $newName = $request->name;

        if ($newName === $menu->name) {
            if ($request->ajax()) {
                return response()->json(['info' => 'No changes detected. The food name is already the same.']);
            }
            return redirect()->back()->with('info', 'No changes detected. The food name is already the same.');
        }

        $exists = FoodMenu::where('name', $newName)
            ->where('id', '!=', $menu->id)
            ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['error' => 'This food name is already taken.']);
            }
            return redirect()->back()->with('error', 'This food name is already taken.');
        }

        $menu->update(['name' => $newName]);

        if ($request->ajax()) {
            return response()->json([
                'success' => "Menu '{$newName}' updated successfully!",
                'updatedName' => $newName,
            ]);
        }

        return redirect()->route('menus.index')->with('success', "Menu '{$newName}' updated successfully!");
    }
    public function destroy(FoodMenu $menu)

    {
        $name = $menu->name;
        $menu->delete();
        return redirect()->route('menus.index')->with('success', "$name is deleted!");
    }
}
