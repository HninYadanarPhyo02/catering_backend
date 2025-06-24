<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\FoodMenu;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonConverterInterface;

class DashboardController extends Controller
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
            ->limit(5)
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
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $results = DB::table('registered_order as ro')
            ->join('attendance as att', function ($join) {
                $join->on('ro.emp_id', '=', 'att.emp_id')->on('ro.date', '=', 'att.date');
            })
            ->join('foodmonthprice as fmp', 'ro.date', '=', 'fmp.date')
            ->whereNull('ro.deleted_at')
            ->whereNull('att.deleted_at')
            ->whereNull('fmp.deleted_at')
            ->whereYear('ro.date', $currentYear)
            ->whereMonth('ro.date', $currentMonth)
            ->select('fmp.food_name', DB::raw('COUNT(*) as total'))
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
        $Month = date('n'); // e.g. 7
        $Year = date('Y');  // e.g. 2025

        $monthlyInvoices = Invoice::with('employee')
            ->where('month', $Month)
            ->where('year', $Year)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();
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
            'ratingsCounts',
            'monthlyInvoices'
        ));
    }
    // public function index()
    // {
    //     $totalmenus = FoodMenu::count();

    //     $monthlymenus = FoodMonthPrice::whereMonth('date', Carbon::now()->month)
    //         ->whereYear('date', Carbon::now()->year)
    //         ->distinct('food_id')
    //         ->count('food_id');

    //     $monthlyavailable = FoodMonthPrice::whereMonth('date', Carbon::now()->month)
    //         ->whereYear('date', Carbon::now()->year)
    //         ->count();

    //     $monthlyorders = RegisteredOrder::whereMonth('date', Carbon::now()->month)
    //         ->whereYear('date', Carbon::now()->year)
    //         ->count();

    //     $totalemployees = Employee::count();

    //     $recentOrders = RegisteredOrder::whereMonth('date', Carbon::now()->month)
    //         ->whereYear('date', Carbon::now()->year)
    //         ->orderBy('date', 'desc')
    //         ->take(5)
    //         ->get();

    //     $totalCheckout = Attendance::whereMonth('date', Carbon::now()->month)
    //         ->whereYear('date', Carbon::now()->year)
    //         ->where('check_out', true)
    //         ->count();

    //     // Line Chart: Daily Sales for last 14 days
    //     $dailySalesRaw = FoodMonthPrice::selectRaw("DATE(date) as day, SUM(price) as total")
    //         ->whereBetween('date', [now()->subDays(13)->startOfDay(), now()->endOfDay()])
    //         ->groupBy('day')
    //         ->orderBy('day')
    //         ->pluck('total', 'day')
    //         ->toArray();

    //     $dailyLabels = [];
    //     $dailySales = [];
    //     for ($i = 13; $i >= 0; $i--) {
    //         $day = now()->subDays($i)->toDateString();
    //         $dailyLabels[] = $day;
    //         $dailySales[] = $dailySalesRaw[$day] ?? 0;
    //     }

    //     // Bar Chart: Top Selling Items (only current year, no soft deleted)
    //     $results = DB::table('registered_order as ro')
    //         ->join('foodmonthprice as fmp', 'ro.date', '=', 'fmp.date')
    //         ->whereNull('fmp.deleted_at')
    //         ->whereNull('ro.deleted_at')
    //         ->whereYear('ro.date', now()->year)
    //         ->select('fmp.food_name', DB::raw('COUNT(ro.date) as total'))
    //         ->groupBy('fmp.food_name')
    //         ->pluck('total', 'fmp.food_name');

    //     $topItemsLabels = array_keys($results->toArray());
    //     $topItemsData = array_values($results->toArray());

    //     // Pie Chart: Feedback Ratings Distribution
    //     $ratingsData = Feedback::selectRaw('rating, COUNT(*) as count')
    //         ->groupBy('rating')
    //         ->orderBy('rating')
    //         ->pluck('count', 'rating')
    //         ->toArray();

    //     $ratingsLabels = array_keys($ratingsData);
    //     $ratingsCounts = array_values($ratingsData);

    //     // Monthly invoices with pagination (20 per page)
    //     $monthlyInvoices = Invoice::with('employee')
    //         ->orderByDesc('year')
    //         ->orderByDesc('month')
    //         ->paginate(20);

    //     return view('dashboard', compact(
    //         'totalmenus',
    //         'monthlymenus',
    //         'monthlyavailable',
    //         'monthlyorders',
    //         'totalemployees',
    //         'recentOrders',
    //         'totalCheckout',
    //         'dailyLabels',
    //         'dailySales',
    //         'topItemsLabels',
    //         'topItemsData',
    //         'ratingsLabels',
    //         'ratingsCounts',
    //         'monthlyInvoices'
    //     ));
    // }
}
