<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoTag;
use App\Models\User;
use App\Models\UserTodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id_user = auth()->id();
        $data = Todo::select('id','activity','start_date','end_date')->where('user_id', $id_user)->orderBy('id')->get();

       return response()->json([
        "status" => "ok",
        "data" => $data,
       ], 200);
    }

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

    public function shareTodo(Request $request){

        $validate = $request->validate([
            'todo_id' => 'required',
            'user_id' => 'required'
        ]);

        $id_self = auth()->id();
        $data_todo = Todo::where('id', $validate['todo_id'])->where('user_id', $id_self)->first();
        $data_user = User::find($validate['user_id']);

        if($data_todo == null){
            return response()->json([
                'message' => 'data todo not found or todo is not yours'
            ], 404);
        }else if($data_user == null){
            return response()->json([
                'message' => 'data user not found'
            ], 404);
        }else if($validate['user_id'] == $id_self){
            return response()->json([
                'message' => 'cannot share todo for self'
            ], 500);
        }

        UserTodo::create($validate);
        return response()->json([
            'message' => 'share todo sucessfully'
        ], 201);
    }

    public function getSharedTodo(Request $request){
        $id_user = auth()->id();
        $data = DB::table('todos')->select('todos.id','todos.activity','todos.start_date','todos.end_date')
        ->join('users_todos','todos.id','=','users_todos.todo_id')
        ->where('users_todos.user_id', $id_user)->get();

        if($data == null){
            return response()->json([
                'message' => 'data not found'
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $data
        ], 200);
    }
}
