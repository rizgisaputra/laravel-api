<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Tag::select('id','tag')->orderBy('id')->get();

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
        $validated = $request->validate([
            'tag' => 'required'
        ]);

        Tag::create($validated);
        return response()->json([
            'message' => 'data create sucessfully'
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Tag::where('id', $id)->first();

        return response()->json([
            'status' => 'ok',
            'data' => $data,
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
          'tag' => 'required'
       ]);

       $data = Tag::find($id);
       if($data == null){
        return response()->json([
            'message' => 'data not found'
        ], 404);
       }

       $result = $data->update($validate);
       if($result == false){
        return response()->json([
            'message'=> 'data failed to update'
        ], 500);
       }

       return response()->json([
        'status' => 'ok',
        'data' => $data
       ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Tag::find($id);
        DB::table('todos_tags')->where('tag_id', $id)->delete();
        if($data == null){
            return response()->json([
                'message'=> 'data not found'
            ]);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'data failed delete'
            ]);
        }

        return response()->noContent();
    }

}
