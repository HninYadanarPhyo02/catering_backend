<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function create(Request $request){
        $validated = $request->validate([
            'date'=>'required|date',
            'text'=>'required|string',
        ]);
        $data = Announcement::create($validated);
        return response()->json([
            'isSuccess'=>true,
            'message' => 'Announcement is successfully setup',
            'data' => $data
        ],200);
    }
    public function show($date){
        $data = Announcement::findByDate($date);
        if($data){
            $data = new AnnouncementResource($data);
            return response([
                'isSuccess' => true,
                'message' => 'Success',
                'data' => $data
            ],200);
        }
        else{
            return response([
                'isSuccess' => false,
                'message' => 'Data is empty',
                'data' => $data
            ],404);
        }
    }
    public function list(){
        $data = Announcement::get();
        if($data){
            $data = AnnouncementResource::collection($data);
            return response([
                'isSuccess' => true,
                'message' => 'Success',
                'data' => $data
            ]);
        }
        else{
            return response([
                'isSuccess' =>  false,
                'message' => 'Fail',
                'data' => $data
            ]);
        }
    }
    public function update(Request $request,$id){
        $data = Announcement::find($id);
        if(!$data){
            return response([
                'isSuccess' => false,
                'message' => 'Id not found',
            ],404);
        }
        $data->update([
            'date'=> $request->date,
            'text' => $request->text,
        ]);
        return response()->json([
            'isSuccess' => true,
            'message' => 'Data is successfully updated',
            'data' => $data
        ],200);
    }
    public function destroy($id){
        $data = Announcement::find($id);
        if(!$data){
            return response([
                'isSuccess' => false,
                'message' => 'Id is not',
            ],404);
        }
        $data->delete();
        return response()->json([
            'isSuccess' => true,
            'message' => 'Successfully deleted'
        ],200);
    }
}
