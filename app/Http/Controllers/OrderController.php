<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Holiday;
use App\Models\Invoice;
use App\Models\FoodMenu;
use App\Models\Attendance;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Display all orders
    // public function index()
    // {
    //     $orders = FoodMonthPrice::all();
    //     $foods = FoodMenu::all();

    //     return view('orders', compact('orders', 'foods'));
    // }


    public function index(Request $request)
    {
        $search = $request->input('search');

        $foods = FoodMenu::all();

        $ordersQuery = FoodMonthPrice::query();

        if ($search) {
            $ordersQuery->where(function ($query) use ($search) {
                // Search by related food name, assuming FoodMonthPrice has 'food_id'
                $query->whereHas('foodMenu', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('date', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
            });
        }

        $orders = $ordersQuery->paginate(5);

        return view('orders', compact('orders', 'foods'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'price' => 'required|numeric',
    //         'items' => 'required|array|min:1',
    //         'items.*.food_id' => 'required|exists:foodmenu,food_id',
    //         'items.*.date' => 'required|date',
    //     ]);

    //     $price = $validated['price'];
    //     $items = $validated['items'];
    //     $inserted = [];
    //     $skipped = [];

    //     foreach ($items as $item) {
    //         $existing = FoodMonthPrice::where('date', $item['date'])->first();
    //         if ($existing) {
    //             $skipped[] = $item['date'];
    //             continue;
    //         }

    //         $food = FoodMenu::where('food_id', $item['food_id'])->first();
    //         $foodName = $food->name ?? 'Unknown';

    //         $order = FoodMonthPrice::create([
    //             'food_id' => $item['food_id'],
    //             'food_name' => $foodName,
    //             'date' => $item['date'],
    //             'price' => $price,
    //         ]);

    //         if ($order) {
    //             $inserted[] = $item['date'];
    //         }
    //     }

    //     if (count($inserted)) {
    //         return redirect('/orders')->with('success', 'Orders added: ' . implode(', ', $inserted));
    //     } else {
    //         return back()->withErrors(['error' => 'No new orders were inserted (possibly all dates already exist).'])->withInput();
    //     }
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'price' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|exists:foodmenu,food_id',
            'items.*.date' => 'required|date',
        ]);

        $price = $validated['price'];
        $items = $validated['items'];
        $inserted = [];
        $skipped = [];

        foreach ($items as $item) {
            $date = $item['date'];

            // Skip if date already exists in FoodMonthPrice
            if (FoodMonthPrice::where('date', $date)->exists()) {
                $skipped[] = "$date (already exists)";
                continue;
            }

            // Skip if date is a holiday
            if (Holiday::where('date', $date)->exists()) {
                $skipped[] = "$date (holiday)";
                continue;
            }

            if (Announcement::where('date', $date)->exists()) {
                $skipped[] = "$date (announcement date)";
                continue;
            }

            $food = FoodMenu::where('food_id', $item['food_id'])->first();
            $foodName = $food->name ?? 'Unknown';

            $order = FoodMonthPrice::create([
                'food_id' => $item['food_id'],
                'food_name' => $foodName,
                'date' => $date,
                'price' => $price,
            ]);

            if ($order) {
                $inserted[] = $date;
            }
        }

        if (count($inserted)) {
            return redirect('/orders')->with('success', 'Orders added: ' . implode(', ', $inserted))
                ->with('skipped', $skipped);
        } else {
            return back()->withErrors(['error' => 'No new orders were inserted (either holiday or already exist).'])
                ->withInput();
        }
    }


    // Show form to edit an order
    public function edit(FoodMonthPrice $order)
    {
        $foods = FoodMenu::all();
        return view('orders.edit', compact('order', 'foods'));
    }


    // Update an order
    public function update(Request $request, FoodMonthPrice $order)
    {
        //   dd($request->all());
        $request->validate([
            'food_id' => 'required|exists:foodmenu,food_id', // Correct table for foreign key validation
            'date' => 'required|date',
            'price' => 'required|numeric'
        ]);
        $food = FoodMenu::where('food_id', $request->food_id)->first();

        // dd($food);
        $order->update([
            'food_id' => $request->food_id,
            'food_name' => $food->name,
            'date' => $request->date,
            'price' => $request->price
        ]);
        // dd($order->toArray());

        // return redirect('/orders')->with('success', 'Order added!');  
        return redirect('/orders')->with('success', 'Order updated successfully.');
    }

    // Delete an order

    // public function destroyByDate($date)
    // {
    //     $food = FoodMonthPrice::whereDate('date', $date)->first();
    //     if (!$food) {
    //         return response()->json(['message' => 'Food item not found'], 404);
    //     }

    //     $dateOnly = Carbon::parse($food->date)->toDateString();
    //     // Debug: Make sure records exist
    //     $rawOrders = DB::table('registered_order')->whereDate('date', $dateOnly)->get();
    
    //     if ($rawOrders->isEmpty()) {
    //         return response()->json(['message' => 'No matching Registered Orders found'], 404);
    //     }

    //     // Delete or force delete
    //     RegisteredOrder::whereDate('date', $dateOnly)->delete(); // or ->forceDelete() if needed
    //     Attendance::whereDate('date', $dateOnly)->delete();
    //     InvoiceDetail::whereDate('date', $dateOnly)->delete();
    //     $food->delete();

    //     return redirect('/orders')->with('success', "Order on $date deleted successfully.");
    // }
    public function destroyByDate($date)
{
    $food = FoodMonthPrice::whereDate('date', $date)->first();
    if (!$food) {
        return response()->json(['message' => 'Food item not found'], 404);
    }

    $dateOnly = Carbon::parse($food->date)->toDateString();

    $rawOrders = DB::table('registered_order')->whereDate('date', $dateOnly)->get();
    if ($rawOrders->isEmpty()) {
        return response()->json(['message' => 'No matching Registered Orders found'], 404);
    }

    // Delete related records
    RegisteredOrder::whereDate('date', $dateOnly)->delete();
    Attendance::whereDate('date', $dateOnly)->delete();
    
    // Get all invoice_ids affected BEFORE deletion
    $invoiceIds = InvoiceDetail::whereDate('date', $dateOnly)->pluck('invoice_id')->unique();

    // Delete invoice_details
    InvoiceDetail::whereDate('date', $dateOnly)->delete();

    // Recalculate total_amount for each invoice
    foreach ($invoiceIds as $invoiceId) {
        $newTotal = InvoiceDetail::where('invoice_id', $invoiceId)->sum('price');
        Invoice::where('invoice_id', $invoiceId)->update(['total_amount' => $newTotal]);
    }

    $food->delete();

    return redirect('/orders')->with('success', "Order on $date deleted successfully.");
}

}
