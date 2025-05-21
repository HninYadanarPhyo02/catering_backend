<?php

// namespace App\Http\Controllers;
namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function index(){
        return response()->json(Feedback::all(),200);
    }
    public function show($emp_id){
        
        $data = Feedback::findByEmpId($emp_id);
        if($data){
            $data = new FeedbackResource($data);
            return response([
                'message' => 'Successfully Stored',
                'data' => $data,
            ],200);
        }
        else{
            return response([
                'message' => 'Fail',
                'data' => $data,
            ]);
        }
        // if(!$data){
        //     return response()->json([
        //         'isSuccess'=>false,
        //         'message'=>'Feedback not found'],404);
        // }
        // return response()->json([
        //     'isSuccess'=>true,
        //     'message'=>'Feedback',
        //     'data'=>$feedback,
        // ],200);
    }
    public function list(){
        $data = Feedback::get();
        if($data){
            $data = FeedbackResource::collection($data);
            return response([
                'message' => 'Success',
                'data' => $data,
            ],200);
        }
        else{
            return response([
                'message' => 'Fail',
                'data' => $data,
            ],404);
        }
        // return response()->json(Feedback::all());
    }
    public function store(Request $request){
        $validated = $request->validate([
            'emp_id'=>'required|string',
            'text'=>'required|string',
            'rating'=>'required|numeric',
        ]);
        $feedback = Feedback::create($validated);
        return response()->json([
            'isSuccess'=>true,
            'message'=>'Feedback created successfully',
            'data'=>$feedback
        ],200);

    }
    public function destroy($fb_id)
    {
        $fb_id = (int) $fb_id;
        $feedback = Feedback::find($fb_id);
    // dd($feedback);


        if (!$feedback) {
            return response()->json([
                'isSuccess'=>false,
                'message' => 'Feedback not found'], 404);
        }

      $feedback->delete(); 
        // dd($aa);

        return response()->json([
            'isSuccess'=>true,
            'message' => 'Feedback deleted successfully'], 200);
    }
    }