<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\FoodMenu;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use function Pest\Laravel\json;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyFoodPriceResource;
use App\Models\Invoice;
use App\Models\InvoiceDetail;

class FoodMonthPriceController extends Controller
{
    public function index()
    {
        return response()->json(FoodMonthPrice::all());
    }

    // public function create(Request $request)
    // {

    //     // dd("here");
    //     // Validate the request
    //     $request->validate([
    //         'price' => 'required|numeric',
    //         'items' => 'required|array|min:1',
    //         'items.*.food_name' => 'required|string',
    //         'items.*.date' => 'required|date',
    //     ]);

    //     $price = $request->price;
    //     $items = $request->items;
    //     $createdItems = [];
    //     // dd($request->toArray());
    //     foreach ($items as $item) {
    //         // dd($item['date']);
    //         $foodName = trim($item['food_name']);
    //         $date = $item['date'];

    //         // Check if this date already has a price record
    //         if (FoodMonthPrice::whereDate('date', Carbon::parse(strtotime($date)))->exists()) {
    //             // Optionally skip or return error
    //             return response()->json([
    //                 'isSuccess' => false,
    //                 'message' => "Date {$date} already defined"
    //             ], 409);
    //         }

    //         // Find or create food menu item
    //         $foodMenu = FoodMenu::firstOrCreate(
    //             ['name' => $foodName],
    //             ['name' => $foodName]
    //         );

    //         // Create food price entry
    //         $foodPrice = FoodMonthPrice::create([
    //             'date' => Carbon::parse(strtotime($date)),
    //             'food_name' => $foodName,
    //             'price' => $price,
    //             'food_id' => $foodMenu->food_id,
    //         ]);

    //         $createdItems[] = $foodPrice;
    //     }

    //     return response()->json([
    //         'isSuccess' => true,
    //         'message' => 'Food items created successfully',
    //         'data' => $createdItems,
    //     ], 201);
    // }

    public function create(Request $request)
    {
        // Validate the request
        $request->validate([
            'price' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.food_name' => 'required|string',
            'items.*.date' => 'required|date',
        ]);

        $price = $request->price;
        $items = $request->items;
        $createdItems = [];

        foreach ($items as $item) {
            $foodName = trim($item['food_name']);
            $date = $item['date'];

            // Check if this date is a holiday
            $isHoliday = \App\Models\Holiday::whereDate('date', $date)->exists();
            if ($isHoliday) {
                return response()->json([
                    'isSuccess' => false,
                    'message' => "Date {$date} is a holiday. Cannot create entry for holiday dates."
                ], 409);
            }

            // Check if this date already has a price record
            $exists = FoodMonthPrice::whereDate('date', $date)->exists();
            if ($exists) {
                return response()->json([
                    'isSuccess' => false,
                    'message' => "Date {$date} already defined in FoodMonthPrice."
                ], 409);
            }

            // Find or create food menu item
            $foodMenu = FoodMenu::firstOrCreate(
                ['name' => $foodName],
                ['name' => $foodName]
            );

            // Create food price entry
            $foodPrice = FoodMonthPrice::create([
                'date' => $date,
                'food_name' => $foodName,
                'price' => $price,
                'food_id' => $foodMenu->food_id,
            ]);

            $createdItems[] = $foodPrice;
        }

        return response()->json([
            'isSuccess' => true,
            'message' => 'Food items created successfully',
            'data' => $createdItems,
        ], 201);
    }

    #select * from foodmonth with food_name
    public function show($date)
    {
        $data = FoodMonthPrice::where('date', $date)->first();

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
        if ($data) {
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
    public function update(Request $request, $date)
    {
        $food = FoodMonthPrice::where('date', $date)->first();
        if (!$food) {
            return response()->json(['message' => 'Food item not found'], 404);
        }
        $menuItem = FoodMenu::where('name', $request->food_name)->first();
        if (!$menuItem) {
            return response()->json(['message' => 'Food name not found in menu'], 404);
        }
        $food->update([
            'food_id' => $menuItem->food_id,
            'food_name' => $menuItem->name,
            'date' => $request->date,
            'price' => $request->price,
        ]);
        return response()->json(['message' => 'Food item updated', 'data' => $food], 200);
    }
    // public function destroy($date)
    // {
    //     // $food = FoodMonthPrice::where('name', $food_name)->first();
    //     $food = FoodMonthPrice::where('date', $date)->first();

    //     if (!$food) {
    //         return response()->json(['message' => 'Food item not found'], 404);
    //     }
    //     $food->delete();
    //     return response()->json(['message' => 'Food item deleted']);
    // }
    public function destroy($date)
{
    $food = FoodMonthPrice::where('date', $date)->first();

    if (!$food) {
        return response()->json(['message' => 'Food item not found'], 404);
    }

    $dateOnly = Carbon::parse($food->date)->toDateString();

    // Debug: Make sure records exist
    $orders = RegisteredOrder::whereDate('date', $dateOnly)->get();

    if ($orders->isEmpty()) {
        return response()->json(['message' => 'No matching Registered Orders found'], 404);
    }

    // Delete or force delete
    RegisteredOrder::whereDate('date', $dateOnly)->delete(); // or ->forceDelete() if needed
    Attendance::whereDate('date', $dateOnly)->delete();
    InvoiceDetail::whereDate('date', $dateOnly)->delete();
    $food->delete();

    return response()->json(['message' => 'Food item and related orders deleted']);
}
}
