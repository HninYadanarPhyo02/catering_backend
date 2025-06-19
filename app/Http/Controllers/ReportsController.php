<?php

namespace App\Http\Controllers;

use App\Models\FoodMenu;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\FoodMonthPrice;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    // public function index()
    // {
    //     // Existing sales by month (last 5 months)
    //     $salesData = FoodMonthPrice::selectRaw("MONTH(date) as month, SUM(price) as total")
    //         ->whereYear('date', date('Y'))
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->pluck('total', 'month')
    //         ->toArray();

    //     // Existing user activity by month (last 5 months)
    //     $userActivityData = RegisteredOrder::selectRaw("MONTH(date) as month, COUNT(*) as count")
    //         ->whereYear('date', date('Y'))
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->pluck('count', 'month')
    //         ->toArray();

    //     // Months names array
    //     $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    //     // Prepare labels, sales, engagement arrays for last 5 months
    //     $labels = [];
    //     $sales = [];
    //     $engagement = [];

    //     $currentMonth = date('n');

    //     for ($i = 4; $i >= 0; $i--) {
    //         $m = $currentMonth - $i;
    //         if ($m <= 0) $m += 12;

    //         $labels[] = $months[$m - 1];
    //         $sales[] = $salesData[$m] ?? 0;
    //         $engagement[] = $userActivityData[$m] ?? 0;
    //     }

    //     // --- New: Top Selling Items this year (top 5) ---
    //     // Assuming RegisteredOrder has 'food_id' and 'quantity' fields,
    //     // and Food model/table has 'name'
    //     // --- New: Top Selling Items from food_month_price ---
    //     $topSellingRaw = FoodMonthPrice::selectRaw('food_name, COUNT(*) as total_sold')
    //         ->whereYear('date', date('Y'))
    //         ->groupBy('food_name')
    //         ->orderByDesc('total_sold')
    //         ->limit(5)
    //         ->get();

    //     $topSellingLabels = $topSellingRaw->pluck('food_name')->toArray();
    //     // $topSellingData = $topSellingRaw->pluck('total_sold')->toArray();


    //     foreach ($topSellingRaw as $item) {
    //         // Lookup food name
    //         $foodName = FoodMenu::find($item->food_id)?->name ?? 'Unknown';

    //         $topSellingLabels[] = $foodName;
    //         $topSellingData[] = $item->total_sold;
    //     }

    //     // --- New: Monthly Sale Trends (last 6 months) ---
    //     $monthlySalesRaw = FoodMonthPrice::selectRaw("MONTH(date) as month, SUM(price) as total")
    //         ->whereBetween('date', [now()->subMonths(5)->startOfMonth(), now()->endOfMonth()])
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->pluck('total', 'month')
    //         ->toArray();

    //     $monthlyTrendLabels = [];
    //     $monthlyTrendData = [];

    //     for ($i = 5; $i >= 0; $i--) {
    //         $m = $currentMonth - $i;
    //         if ($m <= 0) $m += 12;

    //         $monthlyTrendLabels[] = $months[$m - 1];
    //         $monthlyTrendData[] = $monthlySalesRaw[$m] ?? 0;
    //     }

    //     return view('reports_analysis', compact(
    //         'labels',
    //         'sales',
    //         'engagement',
    //         'topSellingLabels',
    //         'topSellingData',
    //         'monthlyTrendLabels',
    //         'monthlyTrendData'
    //     ));
    // }

  public function index()
{
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // 📊 12-month Sales Overview
    $salesDataRaw = DB::table('registered_order as ro')
    ->join('foodmonthprice as fmp', 'ro.date', '=', 'fmp.date')
    ->selectRaw('
        MONTH(ro.date) as month,
        COUNT(*) as total_orders,
        SUM(fmp.price) as unit_price_sum,
        COUNT(*) * AVG(fmp.price) as total_amount
    ')
    ->whereYear('ro.date', 2025)
    ->whereNull('ro.deleted_at')
    ->whereNull('fmp.deleted_at')
    ->groupBy(DB::raw('MONTH(ro.date)'))
    ->pluck('total_amount', 'month')
    ->toArray();


    // 📈 12-month User Engagement
    // $userActivityRaw = RegisteredOrder::selectRaw("MONTH(date) as month, COUNT(*) as count")
    //     ->whereYear('date', date('Y'))
    //     ->groupBy('month')
    //     ->pluck('count', 'month')
    //     ->toArray();
    $userActivityRaw = RegisteredOrder::selectRaw("MONTH(date) as month, COUNT(DISTINCT emp_id) as count")
    ->whereYear('date', date('Y'))
    ->groupBy('month')
    ->pluck('count', 'month')
    ->toArray();


    $labels = $months;
    $sales = [];
    $engagement = [];
    for ($i = 1; $i <= 12; $i++) {
        $sales[] = $salesDataRaw[$i] ?? 0;
        $engagement[] = $userActivityRaw[$i] ?? 0;
    }

    // 🍔 Top Selling Items (unchanged)
    $topSellingRaw = FoodMonthPrice::selectRaw('food_name, COUNT(*) as total_sold')
        ->whereYear('date', date('Y'))
        ->groupBy('food_name')
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();

    $topSellingLabels = $topSellingRaw->pluck('food_name')->toArray();
    $topSellingData = $topSellingRaw->pluck('total_sold')->toArray();

    // 📅 Monthly Registered & Attendance
    $monthlyRegisteredRaw = RegisteredOrder::selectRaw("MONTH(date) as month, COUNT(*) as total")
        ->whereYear('date', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $monthlyAttendanceRaw = Attendance::selectRaw("MONTH(date) as month, COUNT(*) as total")
        ->where('check_out', 1)
        ->whereYear('date', date('Y'))
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $monthlyTrendLabels = $months;
    $monthlyRegisteredData = [];
    $monthlyAttendanceData = [];

    for ($i = 1; $i <= 12; $i++) {
        $monthlyRegisteredData[] = $monthlyRegisteredRaw[$i] ?? 0;
        $monthlyAttendanceData[] = $monthlyAttendanceRaw[$i] ?? 0;
    }

    return view('reports_analysis', compact(
        'labels',
        'sales',
        'engagement',
        'topSellingLabels',
        'topSellingData',
        'monthlyTrendLabels',
        'monthlyRegisteredData',
        'monthlyAttendanceData'
    ));
}


}
