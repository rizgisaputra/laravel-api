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
        $id_user = auth()->id();
        $data = Tag::select('id','tag','user_id')->orderBy('id')->where('user_id', $id_user)->get();

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
            'tag' => 'required',
        ]);

        $validated['user_id'] = auth()->id();
        $result = Tag::create($validated);

        if($result == false){
           return response()->json([
            'message' => 'failed to create data'
           ], 500);
        }

        return response()->json([
            'message' => 'data create sucessfully'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id_user = auth()->id();
        $data = Tag::where('id', $id)->where('user_id', $id_user)->first();

        if($data != null){
            return response()->json([
                'status' => 'ok',
                'data' => $data,
            ], 200);
        }

        return response()->json([
            'message' => 'data not found'
        ], 404);
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

       $id_user = auth()->id();
       $data = Tag::where('id', $id)->where('user_id', $id_user)->first();

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
        DB::table('todos_tags')->where('tag_id', $id)->delete();
        $id_user = auth()->id();
        $data = Tag::where('id', $id)->where('user_id', $id_user)->first();

        if($data == null){
            return response()->json([
                'message'=> 'data not found'
            ], 404);
        }

        $result = $data->delete();
        if($result == false){
            return response()->json([
                'message' => 'data failed delete'
            ], 500);
        }

        return response()->noContent();
    }

}
