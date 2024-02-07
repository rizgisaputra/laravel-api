<?php

namespace App\Http\Controllers;

use App\Models\TodoTag;
use Illuminate\Http\Request;

class TodoTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = TodoTag::select('id','todo_id','tag_id')->orderBy('id')->get();

       return response()->json([
        "status" => "ok",
        "data" => $data,
       ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'todo_id' => 'required',
            'tag_id' => 'required'
        ]);

        TodoTag::create($validate);
        return response()->json([
            'message' => 'data create sucessfully'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = TodoTag::where('id', $id)->first();
        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'todo_id' => 'required',
            'tag_id' => 'required'
        ]);

        $data = TodoTag::find($id);
        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ]);
        }

        $result = $data->update($validate);
        if($result == false){
            return response()->json([
                'message' => 'failed to update data'
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = TodoTag::find($id);
        if($data == null){
            return response()->json([
                 'message'=> 'todo not found'
            ], 404);
        }

        $result = $data->delete();
        if($result == true){
            return response()->json([
                'message' => 'data delete sucessfully'
            ]);
        }

        return response()->noContent();
    }
}
