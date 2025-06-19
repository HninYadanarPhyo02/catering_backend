<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\FoodMenu;
use App\Models\Attendance;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $totalmenus = FoodMenu::count();
        $monthlymenus = FoodMonthPrice::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->distinct('food_id')
            ->count('food_id');


        $monthlyavailable = FoodMonthPrice::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->count();

        $monthlyorders = RegisteredOrder::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->count();

        $totalemployees = Employee::count();

        $recentOrders = RegisteredOrder::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $totalCheckout = Attendance::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('check_out', true)
            ->count();

        // Line Chart: Daily Sales
        $dailySalesRaw = FoodMonthPrice::selectRaw("DATE(date) as day, SUM(price) as total")
            ->whereBetween('date', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $dailyLabels = [];
        $dailySales = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $dailyLabels[] = $day;
            $dailySales[] = $dailySalesRaw[$day] ?? 0;
        }

        // Bar Chart: Top Selling Items from actual registered_order
        $results = DB::table('registered_order as ro')
            ->join('foodmonthprice as fmp', 'ro.date', '=', 'fmp.date')
            ->whereNull('fmp.deleted_at')
            ->select('fmp.food_name', DB::raw('COUNT(ro.date) as total'))
            ->groupBy('fmp.food_name')
            ->pluck('total', 'fmp.food_name');

        $topItemsLabels = array_keys($results->toArray());
        $topItemsData = array_values($results->toArray());

        // Pie Chart: Feedback Ratings Distribution
        $ratingsData = Feedback::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $ratingsLabels = array_keys($ratingsData);
        $ratingsCounts = array_values($ratingsData);

        return view('dashboard', compact(
            'totalmenus',
            'monthlymenus',
            'monthlyavailable',
            'monthlyorders',
            'totalemployees',
            'recentOrders',
            'totalCheckout',
            'dailyLabels',
            'dailySales',
            'topItemsLabels',
            'topItemsData',
            'ratingsLabels',
            'ratingsCounts'
        ));
    }
}