<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\DailyFoodPriceResource;
use App\Models\FoodMonthPrice;
use Illuminate\Http\Request;
use Carbon\Carbon;

use function Pest\Laravel\json;

class FoodMonthPriceController extends Controller
{
    public function index(){
        return response()->json(FoodMonthPrice::all());
    }
    public function create(Request $request){
        
    // Check if 'name' is empty
    if (!$request->food_name || trim($request->food_name) === '' ) {
        return response()->json([
            'isSuccess' => false,
            'message' => 'Food name is empty, please add'
        ], 400);
    }
    if(!$request->date || trim($request->date)==''){
        return response()->json([
            'isSuccess' => false,
            'message' =>'Date is empty'
        ],400);
    }
    if(!$request->price || trim($request->price)==''){
        return response()->json([
            'isSuccess' => false,
            'message' =>'Price is empty'
        ],400);
    }
    $exists = FoodMonthPrice::findByName($request->food_name)->exists();

    if ($exists) {
        return response()->json([
            'isSuccess' => false,
            'message' => 'This curry already exists'
        ], 409);
    }
        $food = FoodMonthPrice::create([
            'date'=> $request->date,
            'food_name'=>$request->food_name,
            'price'=>$request->price,
        ]);
        return response()->json(['isSuccess' => true,'message'=>'Data is created','data'=>$food],200);

    }
    #select * from foodmonth with food_name
    public function show($food_name)
    {
        $data = FoodMonthPrice::where('food_name', $food_name)->first();

        if ($data) {
            $data = new DailyFoodPriceResource($data);
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
        // $food = FoodMonthPrice::where('food_name', $food_name)->first();

        // if(!$food){
        //     return response()->json(['message'=>'Food item not found'],404);

        // }
        // return response()->json($food);
    }
    //To select * from foodmonth
     public function list()
    {
        $data = FoodMonthPrice::get();
        if($data){
            $data = DailyFoodPriceResource::collection($data);
        }
        return response([
            'code' => 200,
            'message' => 'Success',
            'data' => $data
        ]);
        // return response()->json(FoodMonthPrice::all());

    }
    #Update a food itemr
    public function update(Request $request, $food_name)
    {
        $food = FoodMonthPrice::findByName($food_name);
        if(!$food){
            return response()->json(['message'=>'Food item not found'],404);
        }
        $food->update([
            'date'=>$request->date,
            'food_name'=>$request->food_name,
            'price'=>$request->price,
        ]);
        return response()->json(['message'=>'Food item updated','data'=>$food]);

    }
    public function destroy($food_name) {
    // $food = FoodMonthPrice::where('name', $food_name)->first();
     $food = FoodMonthPrice::findByName($food_name);

    if (!$food) {
        return response()->json(['message' => 'Food item not found'], 404);
    }

    $food->delete();
    return response()->json(['message' => 'Food item deleted']);
}

}
