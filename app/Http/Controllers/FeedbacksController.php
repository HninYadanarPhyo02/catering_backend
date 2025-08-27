<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbacksController extends Controller
{
    public function index(Request $request)
    {
        $empId = $request->query('emp_id');
        $search = $request->query('search');

        $feedbackQuery = Feedback::with('employee');

        if ($empId) {
            $feedbackQuery->where('emp_id', $empId);
        }

        if ($search) {
            $feedbackQuery->where(function ($query) use ($search) {
                $query->where('emp_id', 'like', '%' . $search . '%')
                    ->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $feedbackList = $feedbackQuery
            ->select('emp_id', DB::raw('count(*) as total_feedback'))
            ->groupBy('emp_id')
            ->with('employee')
            ->paginate(10)
            ->appends($request->query()); // Keep filters during pagination

        $employees = Employee::whereIn('emp_id', function ($query) {
            $query->select('emp_id')
                ->from('feedback')
                ->groupBy('emp_id')
                ->havingRaw('COUNT(*) >= 1');
        })->where('role', '<>', 'admin')->get();


        return view('feedback.index', compact('feedbackList', 'employees'));
    }

    public function details(Request $request, $emp_id)
    {
        $employee = Employee::where('emp_id', $emp_id)->firstOrFail();

        $feedbackQuery = Feedback::where('emp_id', $emp_id);

        // Filter by month and year
        if ($request->filled('month')) {
            $feedbackQuery->whereMonth('updated_at', $request->input('month'));
        }

        if ($request->filled('year')) {
            $feedbackQuery->whereYear('updated_at', $request->input('year'));
        }

        $feedbackList = $feedbackQuery->orderBy('updated_at', 'desc')->paginate(10);

        $availableYears = Feedback::where('emp_id', $emp_id)
            ->selectRaw('YEAR(updated_at) as year')
            ->distinct()
            ->pluck('year');

        return view('feedback.details', compact('employee', 'feedbackList', 'availableYears'));
    }
}
