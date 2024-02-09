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
        $id_user = auth()->id();
        $data = Todo::select('id','activity','start_date','end_date')->where('user_id', $id_user)->orderBy('id');

        if($query != null){
            $data->where('activity', $query);
        }

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
            'activity' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'tags' => 'array|required',
            'tags.*.id' => 'required'
        ]);

        $validated['is_done'] = false;
        $validated['user_id'] = auth()->id();
        $tags = $validated['tags'];
        unset($validated['tags']);

        DB::beginTransaction();
        try{
            $result = Todo::create($validated);

            $createdTags = [];
            foreach($tags as $tag){
                $dt = [
                    'todo_id' => $result->id,
                    'tag_id' => $tag['id']
                ];

                $createdTags[] = TodoTag::create($dt);
            }

            $result->tags = $createdTags;
            DB::commit();
        }catch(\Throwable $th){
            DB::rollBack();
            return response()->json([
                'message' => 'data failed'
            ], 500);
        }

        return response()->json([
            'message' => 'data create sucessfully',
            'data' => $result
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $id_user = auth()->id();
        $tag = DB::table('tags')->select('tags.id','tags.tag')
        ->join('todos_tags','tags.id','=','todos_tags.tag_id')->where('todo_id', $id)->get();
        $data = Todo::where('id', $id)->where('user_id', $id_user)->first();

        $dataArray = [
            'todo' => $data,
            'tag' => $tag
        ];

        if($dataArray != null){
            return response()->json([
                'status' => 'ok',
                'data' => $dataArray,
            ], 200);
        }else{
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }
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

        $id_user = auth()->id();
        $data = Todo::where('id', $id)->where('user_id', $id_user)->first();
        if($data == null){
            return response()->json([
                 'message'=> 'todo not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'data failed to upadate'
            ],500);
        }

        return response()->json([
            'status' => 'ok',
            'data'=> $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id_user = auth()->id();
        $data = Todo::where('id', $id)->where('user_id', $id_user)->first();
        DB::table('todos_tags')->where('todo_id',$id)->delete();
        if($data == null){
            return response()->json([
                 'message'=> 'todo not found'
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

    public function updateIsDone(Request $request, string $id){
        $validated = $request->validate([
            'is_done' => 'required',
        ]);

        $id_user = auth()->id();
        $data = Todo::where('id', $id)->where('user_id', $id_user)->first();
        if($data == null){
            return response()->json([
                 'message'=> 'todo not found'
            ], 404);
        }

        $result = $data->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'data failed to upadate'
            ],500);
        }

        return response()->json([
            'status' => 'ok',
            'data'=> $data
        ]);
    }
}
