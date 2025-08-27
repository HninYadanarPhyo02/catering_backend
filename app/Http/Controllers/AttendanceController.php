<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Attendance::query();
    //     $employees = Employee::where('emp_id')->first();

    //     if ($request->filled('search')) {
    //         $query->where('emp_id', 'like', "%{$request->search}%")
    //             ->orWhereHas('employee', function ($q) use ($request) {
    //                 $q->where('name', 'like', "%{$request->search}%");
    //             });
    //     }

    //     $attendanceList = $query->latest()->paginate(10);

    //     return view('attendance', compact('attendanceList','employees'));
    // }
    public function index(Request $request)
{
    $empId = $request->query('emp_id');

    $attendanceQuery = Attendance::with('employee');

    if ($empId) {
        $attendanceQuery->where('emp_id', $empId);
    }

    $attendanceSummary = $attendanceQuery
        ->select('emp_id', DB::raw('count(*) as record_count'))
        ->groupBy('emp_id')
        ->with('employee')
        ->paginate(10);

    $employees = Employee::all();
    $attendance = Attendance::orderby('created_at','desc')->paginate(4);

    return view('attendance.index', compact('attendanceSummary', 'employees','attendance'));
}
public function details(Request $request, $emp_id)
{
    $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

    $attendanceQuery = Attendance::with('foodmonthpriceByDate')
        ->where('emp_id', $emp_id);

    // Filter by month and year
    if ($request->filled('month')) {
        $attendanceQuery->whereMonth('date', $request->input('month'));
    }

    if ($request->filled('year')) {
        $attendanceQuery->whereYear('date', $request->input('year'));
    }

    $attendanceRecords = $attendanceQuery
        ->orderBy('date', 'desc')
        ->paginate(10);

    // ✅ This is the key part you're missing
    $availableYears = Attendance::where('emp_id', $emp_id)
        ->selectRaw('YEAR(date) as year')
        ->distinct()
        ->pluck('year');

    return view('attendance.details', compact('employee', 'attendanceRecords', 'availableYears'));
}

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'employee_name' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|string'
        ]);

        Attendance::create($request->all());

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded.');
    }
}
