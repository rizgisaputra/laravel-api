<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = request()->query('search');
        $data = Todo::select('id','activity','start_date','end_date')->orderBy('id')->get();

        if($query != null){
            $data->where('activity', $query);
        }

        $data = $data->get();

       return response()->json([
        "status" => "ok",
        "data" => $data,
       ], 200);
    //    return view('todo', ['data'=> $data]);
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
            'activity' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'tags' => 'array|required',
            'tags.*.id' => 'required'
        ]);

        $validated['is_done'] = false;
        $tags = $validated['tags'];
        unset($validated['tags']);

        DB::beginTransaction();
        try{
            $result = Todo::create($validated);

            $createdTags = [];
            foreach($tags as $tag){
                $dt = [
                    'todo_id' => $result->id,
                    'tag_id' => $tag["id"]
                ];

                $createdTags[] = TodoTag::create($dt);
            }

            $result->tags = $createdTags;
            DB::commit();
        }catch(\Throwable $th){
            DB::rollBack();
            return response()->json([
                'message' => 'data failed'
            ]);
        }

        return response()->json([
            'message' => 'data create sucessfully',
            'data' => $result
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Todo::where('id', $id)->first();

        return response()->json([
            'status' => 'ok',
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'activity' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $data = Todo::find($id);
        if($data == null){
            return response()->json([
                 'message'=> 'todo not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'data failed to upadate'
            ],400);
        }
        return response()->json([
            'status' => 'ok',
            'data'=> $data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Todo::find($id);
        DB::table('todos_tags')->where('todo_id',$id)->delete();
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

    public function updateIsDone(Request $request, string $id){
        $validated = $request->validate([
            'is_done' => 'required',
        ]);

        $data = Todo::find($id);
        if($data == null){
            return response()->json([
                 'message'=> 'todo not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'data failed to upadate'
            ],400);
        }
        return response()->json([
            'status' => 'ok',
            'data'=> $data
        ]);
    }
}
