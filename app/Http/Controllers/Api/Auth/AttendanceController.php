<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class AttendanceController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            'emp_id' => 'required|string',
            'date' => 'required|date',
        ]);
        $data = Attendance::create($validated);
        return response()->json([
            'isSuccess' => true,
            'message' => 'Attendance is successfully stored',
            'data' => $data,
        ],200);
    }
    public function show($emp_id){
        $data = Attendance::findByEmpId($emp_id);
        if($data){
            $data =new AttendanceResource($data);
            return response([
                'message' => 'Success',
                'data' => $data
            ],200);
        }
        else{
            return response([
                'message' => 'Your emp_id is empty, try again!',
                'data' => $data,
            ],404);
        }
    }
    public function list(){
        $data = Attendance::get();
        if($data){
            $data = AttendanceResource::collection($data);
            return response([
                'message' => 'Success',
                'data' => $data,
            ],200);
        }
        return response(
            [
                'message' => 'Data is empty',
                'data' => $data,
            ]
        );
    }
    public function destroy($id){
        $id = Attendance::find($id);
        if(!$id){
            return response()->json([
                'message' => 'Id not found',
                ''
            ],404);
        } 
        $id->delete();
        return response()->json([
            'message' => 'Successfully deleted',
        ],200);
}
}