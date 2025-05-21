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
    public function store(Request $request)
    {

        // Check if 'name' is empty
        if (!$request->name || trim($request->name) === '') {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Food name is empty, please add'
            ], 400);
        }

        // Check for duplicate food name (case-insensitive)
        $exists = FoodMenu::findByName($request->name)->exists();

        if ($exists) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'This curry already exists'
            ], 409);
        }
        $food = FoodMenu::create([
            'name' => $request->name,
        ]);
        return response()->json(['isSuccess' => true, 'message' => 'Food item created', 'data' => $food], 200);
    }
    public function show($name)
    {

        $data = FoodMenu::where('name', $name)->first();

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
        $food = FoodMenu::findByName($food_name);
        // $food = FoodMenu::where('name', $food_name)->first();
        if (!$food) {
            return response()->json(['message' => 'Food item not found'], 404);
        }
        $food->delete();
        return response()->json(['message' => 'Food item deleted'],200);
    }
}
