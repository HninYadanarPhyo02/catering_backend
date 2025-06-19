<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function index()
    {
        return response()->json(Feedback::all(), 200);
    }
    // public function show($emp_id){

    //     // $data = Feedback::findByEmpId($emp_id);
    //     $data = Feedback::where('emp_id', $emp_id)->get(); // Return collection

    //       if ($data->isNotEmpty()) {
    //             return response([
    //                 'message' => 'Successfully Retrieved',
    //                 'data' => FeedbackResource::collection($data), // Convert to array
    //             ], 200);
    //         } else {
    //             return response([
    //                 'message' => 'Fail',
    //                 'data' => [],
    //             ], 200);
    //         }
    // }

    public function show($emp_id)
    {
        // Retrieve feedbacks for emp_id with employee info
        $data = Feedback::with('employee')->where('emp_id', $emp_id)->get();

        if ($data->isNotEmpty()) {
            return response([
                'message' => 'Successfully Retrieved',
                'data' => FeedbackResource::collection($data),
            ], 200);
        } else {
            return response([
                'message' => 'Fail',
                'data' => [],
            ], 200);
        }
    }



    //     public function show($emp_id)
    // {
    //     $data = Feedback::where('emp_id', $emp_id)->get();

    //     if ($data->isNotEmpty()) {
    //         return response()->json([
    //             'message' => 'Successfully Retrieved',
    //             'data' => [
    //                 'feedbacks' => FeedbackResource::collection($data)  // <== Wrap in object
    //             ]
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'message' => 'Fail',
    //             'data' => [
    //                 'feedbacks' => []
    //             ]
    //         ], 404);
    //     }
    // }
    public function list()
    {
        $data = Feedback::get();
        if ($data) {
            $data = FeedbackResource::collection($data);
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
        // return response()->json(Feedback::all());
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_id' => 'required|string',
            'text' => 'required|string',
            'rating' => 'required|numeric',
        ]);
        $feedback = Feedback::create($validated);
        return response()->json([
            'isSuccess' => true,
            'message' => 'Feedback created successfully',
            'data' => $feedback->toArray(),
        ], 200);
        //     return response(print_r([
        //     'isSuccess' => true,
        //     'message' => 'Feedback created successfully',
        //     'data' => $feedback->toArray(),
        // ], true), 200)->header('Content-Type', 'text/plain');
    }
    public function destroy($fb_id)
    {
        $fb_id = (int) $fb_id;
        $feedback = Feedback::find($fb_id);
        // dd($feedback);


        if (!$feedback) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Feedback not found'
            ], 404);
        }

        $feedback->delete();
        // dd($aa);

        return response()->json([
            'isSuccess' => true,
            'message' => 'Feedback deleted successfully'
        ], 200);
    }
}
