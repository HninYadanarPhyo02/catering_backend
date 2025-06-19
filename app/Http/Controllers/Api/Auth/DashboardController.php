<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\FoodMenu;

class DashboardController extends Controller
{
    //  public function stats()
    // {
    //     $totalEmployees = Employee::count();

    //     $totalOrders = RegisteredOrder::count();

    //     $monthlyOrders = RegisteredOrder::whereMonth('date', Carbon::now()->month)
    //                           ->whereYear('date', Carbon::now()->year)
    //                           ->count();

    //     return response()->json([
    //         'total_employees' => $totalEmployees,
    //         'total_orders' => $totalOrders,
    //         'monthly_orders' => $monthlyOrders,
    //     ],200);
    // }
    public function stats()
{
    $totalEmployees = Employee::count();

    $totalOrders = RegisteredOrder::count();
    $monthlyOrders = RegisteredOrder::whereMonth('date', Carbon::now()->month)
                      ->whereYear('date', Carbon::now()->year)
                      ->count();

    // Count distinct menus (food_id) attended in current month
    $currentYear = Carbon::now()->year;
    $currentMonth = Carbon::now()->month;


    // $menusCount = FoodMonthPrice::whereYear('date', $currentYear)
    // ->whereMonth('date', $currentMonth)
    // ->distinct('food_id')
    // ->count('food_id');
    $menusCount = FoodMenu::count();

    return response()->json([
        'total_employees' => $totalEmployees,
        'total_orders' => $totalOrders,
        'monthly_orders' => $monthlyOrders,
        'menus_count' => $menusCount, // your new count here
    ], 200);
}
public function getMonthlyRegisteredMenus()
{
    $results = DB::table('registered_order as ro')
        ->join('foodmonthprice as fmp', 'ro.date', '=', 'fmp.date')
        ->whereNull('fmp.deleted_at')
        ->select('fmp.food_name', DB::raw('COUNT(ro.date) as total'))
        ->groupBy('fmp.food_name')
        ->pluck('total', 'fmp.food_name');

    return response()->json($results, 200);
}

}
