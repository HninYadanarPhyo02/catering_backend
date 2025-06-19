<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoodResource;
use App\Models\FoodMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FoodController extends Controller
{
    public function list()
    {
        $data = FoodMenu::get();
        if ($data) {
            $data = FoodResource::collection($data);
        }
        return response([
            'code' => 200,
            'message' => 'Success',
            'data' => $data,
        ]);
        // return response()->json(FoodMenu::all());

    }
    // public function store(Request $request)
    // {

        
    //     $lastmenu = FoodMenu::orderByRaw("CAST(SUBSTRING(food_id, 6) AS UNSIGNED) DESC")->first();

    //     $lastNumber = $lastmenu ? intval(substr($lastmenu->food_id, 5)) : 0; // Note: substr start at 5 (0-based index)

    //     $newNumber = $lastNumber + 1;

    //     $newfoodId = 'food_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    //     // Check if 'name' is empty
    //     if (!$request->name || trim($request->name) === '') {
    //         return response()->json([
    //             'isSuccess' => false,
    //             'message' => 'Food name is empty, please add'
    //         ], 404);
    //     }

    //      $food = FoodMenu::firstOrCreate(
    //         ['name' => $request->name],
    //         ['food_id' => $newfoodId]  // Assign UUID if creating new
    //     );
    //     // Check for duplicate food name (case-insensitive)
    //     $exists = FoodMenu::where('name',$request->name)->exists();

    //     if (!$exists) {
    //         $food = FoodMenu::create([
    //         'food_id' => $newfoodId,
    //         'name' => $request->name,
    //     ]);
    //     return response()->json(['isSuccess' => true, 'message' => 'Food item created', 'data' => $food], 200);
    //     }        
    //     return response()->json([
    //             'isSuccess' => false,
    //             'message' => 'This curry already exists'
    //         ], 404);
    // }

    public function store(Request $request)
    {
        if (!$request->name || trim($request->name) === '') {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Food name is empty, please add'
            ], 400);
        }
        // raw query 
        $existingFood = FoodMenu::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();

        if ($existingFood) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'This curry already exists'
            ], 409);
        }

        $lastMenu = FoodMenu::orderByRaw("CAST(SUBSTRING(food_id, 6) AS UNSIGNED) DESC")->first();
        $lastNumber = $lastMenu ? intval(substr($lastMenu->food_id, 5)) : 0;
        $newNumber = $lastNumber + 1;
        $newFoodId = 'food_' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // Create the new food item
        $food = FoodMenu::create([
            'food_id' => $newFoodId,
            'name' => $request->name,
        ]);

        return response()->json([
            'isSuccess' => true,
            'message' => 'Food item created',
            'data' => $food
        ], 200);
    }

    public function show($name)
    {

        // $data = FoodMenu::where('name', $name)->first();
$data = FoodMenu::with('foodMonthPrices')->where('name', $name)->first();
    //   dd($data);

        if ($data) {
            $data = new FoodResource($data);
            return response([
                'message' => 'Success',
                'data' => $data,
            ], 200);
        } else {
            return response([
                'message' => 'Fail',
                'data' => $data,
            ], 404);
        }


        // $food = FoodMenu::where('name', $name)->first();

        // if (!$food) {
        //     return response()->json(['message' => 'Food item not found'], 404);
        // }

        // return response()->json($food);
    }
    #Update a food itemr
    public function update(Request $request, $name)
    {
        $food = FoodMenu::findByName($name);
        // $food = FoodMenu::all();
        // $food = FoodMenu::where('food_id', $food_id)->first();
        // $food = FoodMenu::all();
        // $food = FoodMenu::where('name', $name)->first();

        if (!$food) {
            return response()->json(['message' => 'Food item not found'], 404);
        }
        // $request->validate([
        //     'name'=>'required|string',
        // ]);
        $food->update([
            'name' => $request->name
        ]);
        return response()->json(['message' => 'Food item updated', 'data' => $food]);
    }

    #Delete a food item 
    public function destroy($food_name)
    {
        $food = FoodMenu::where('food_name',$food_name)->first();
        // $food = FoodMenu::where('name', $food_name)->first();
        if (!$food) {
            return response()->json(['message' => 'Food item not found'], 404);
        }
        $food->delete();
        return response()->json(['message' => 'Food item deleted'],200);
    }
}
