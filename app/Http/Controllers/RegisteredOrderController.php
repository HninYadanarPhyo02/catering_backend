<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\RegisteredOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FoodMonthPrice;
use App\Models\FoodMonthPrice as ModelsFoodMonthPrice;

class RegisteredOrderController extends Controller
{
    // public function index()
    // {
    //     $orderSummary = RegisteredOrder::with('employee')
    //         ->select('emp_id', DB::raw('count(*) as order_count'))
    //         ->groupBy('emp_id')
    //         ->get()
    //         ->map(function ($item) {
    //             $item->employee_name = optional($item->employee)->name ?? 'N/A';
    //             return $item;
    //         });
    //     $employees = Employee::all();
    //     $menus = ModelsFoodMonthPrice::pluck('food_name')->unique();

    //     return view('registeredorder', compact('orderSummary', 'employees', 'menus'));
    // }
    public function index(Request $request)
    {
        $employees = Employee::all(); // Make sure role filtering is applied if needed

        // Build base query for order summary
        $query = RegisteredOrder::query()
            ->select('emp_id', DB::raw('count(*) as order_count'))
            ->groupBy('emp_id')
            ->with('employee:id,emp_id,name'); // eager load employee relation if exists

        // Apply employee filter if selected
        if ($request->filled('emp_id')) {
            $query->where('emp_id', $request->emp_id);
        }

        // Execute query and get summary
        $orderSummary = $query->paginate(3);
        $menus = ModelsFoodMonthPrice::pluck('food_name')->unique();


        return view('registeredorder', compact('employees', 'orderSummary','menus'));
    }


    // public function index(Request $request)
    // {
    //     $selectedEmpId = $request->input('emp_id');
    //     $selectedEmpName = $request->input('emp_name');
    //     $selectedDate = $request->input('date');
    //     $selectedMenu = $request->input('menu');

    //     // Only get employees whose role is not 'admin'
    //     $employees = Employee::where('role', '!=', 'admin')->get();
    //     $menus = ModelsFoodMonthPrice::select('food_name')->distinct()->pluck('food_name');

    //     $query = RegisteredOrder::with(['employee', 'foodMonthPricesByDate']);

    //     if ($selectedEmpId) {
    //         $query->where('emp_id', $selectedEmpId);
    //     }

    //     if ($selectedEmpName) {
    //         $query->whereHas('employee', function ($q) use ($selectedEmpName) {
    //             $q->where('name', 'like', '%' . $selectedEmpName . '%');
    //         });
    //     }

    //     if ($selectedDate) {
    //         $query->whereDate('date', $selectedDate);
    //     }

    //     if ($selectedMenu) {
    //         $query->whereHas('foodMonthPricesByDate', function ($q) use ($selectedMenu) {
    //             $q->where('food_name', 'like', '%' . $selectedMenu . '%');
    //         });
    //     }

    //     $registeredOrders = $query->paginate(10);

    //     return view('registeredorder', compact(
    //         'registeredOrders',
    //         'employees',
    //         'menus',
    //         'selectedEmpId',
    //         'selectedEmpName',
    //         'selectedDate',
    //         'selectedMenu'
    //     ));
    // }

    // Optional: Show form to create new registered order
    public function create()
    {
        // Fetch employees for dropdown or selection
        $employees = Employee::all();

        return view('registeredorder_create', compact('employees'));
    }

    // Store new registered order
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_id' => 'required|exists:employees,emp_id',
            'date' => 'required|date',
        ]);

        try {
            RegisteredOrder::register($validated['emp_id'], $validated['date']);
            return redirect()->route('registeredorder.index')->with('success', 'Order registered successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Optional: Show single registered order details
    // public function show($id)
    // {
    //     $order = RegisteredOrder::with('employee')->findOrFail($id);
    //     return view('registeredorder_show', compact('order'));
    // }

    public function show($id)
    {
        $order = RegisteredOrder::with(['employee', 'foodMonthPricesByDate'])->findOrFail($id);

        return view('registered-orders.show', compact('order'));
    }

    // Optional: Delete registered order
    public function destroy($id)
    {
        $order = RegisteredOrder::findOrFail($id);
        $order->delete();

        return redirect()->route('registeredorder.index')->with('success', 'Order deleted successfully.');
    }
    // public function showByEmployee($emp_id)
    // {
    //     $employee = Employee::findOrFail($emp_id);

    //     $orders = RegisteredOrder::with('foodMonthPricesByDate')
    //         ->where('emp_id', $emp_id)
    //         ->orderBy('date', 'desc')
    //         ->get();

    //     return view('employee_details', compact('employee', 'orders'));
    // }
    public function showByEmployee($emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

        $orders = RegisteredOrder::with('foodMonthPricesByDate')
            ->where('emp_id', $emp_id)
            ->when(request('date'), fn($query) => $query->whereDate('date', request('date')))
            ->when(request('menu'), function ($query) {
                $query->whereHas('foodMonthPricesByDate', function ($q) {
                    $q->where('food_name', request('menu'));
                });
            })
            ->paginate(3);

        $menus = ModelsFoodMonthPrice::distinct()->pluck('food_name');

        return view('employee_details', compact('employee', 'orders', 'menus'));
    }
}
