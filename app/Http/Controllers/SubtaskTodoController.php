<?php

namespace App\Http\Controllers;

use App\Models\SubtaskTodo;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;

class SubtaskTodoController extends Controller
{
    public function index(Request $request){
        $data = SubtaskTodo::select('id', 'subtask', 'todo_id', 'user_id','is_done')->get();
        return response()->json([
            'status' == 'ok',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subtask' => 'required',
            'todo_id' => 'required'
        ]);

        $validated['is_done'] = false;
        $validated['user_id'] = null;
        $todo_id = Todo::find($validated['todo_id']);
        if($todo_id == null){
            return response()->json([
                'message' => 'todo not found'
            ], 404);
        }

        $result = SubtaskTodo::create($validated);
        if($result == false){
            return response()->json([
                'status' => 'error',
                'message' => 'failed to create subtask'
            ], 500);
        }

        return response()->json([
            'message' => 'data create sucessfully'
        ], 201);
    }

    public function show(string $id){
        $data = SubtaskTodo::select('id', 'subtask', 'todo_id', 'user_id','is_done')
        ->where('todo_id', $id)->get();

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

    public function setResponsible(Request $request, string $id){
        $validated = $request->validate([
            'user_id' => 'required'
        ]);

        $data_user = User::find($validated['user_id']);
        $data_task = SubtaskTodo::find($id);
        if($data_task == null){
            return response()->json([
                'message' => 'data task not found'
            ], 404);
        }else if($data_user == null){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }

        $result = $data_task->update($validated);
        if($result == false){
            return response()->json([
                'message' => 'failed set responsible'
            ], 500);
        }

        return response()->json([
            'message' => 'set responsible sucessfully'
        ], 200);
    }

    public function isDone(Request $request, string $id)
    {
        $task = SubtaskTodo::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'task not found',
            ]);
        }

        $todo = $task['todo_id'];
        $tasks = SubtaskTodo::select('is_done')->where('todo_id', $todo)->where('is_done', false)->get();
        $todos = Todo::where('id', $todo);

        if (count($tasks) <= 1) {
            $array = [
                "is_done" => true,
            ];
            $todos->update($array);
        }

        $task->is_done = true;
        $task->save();

        return response()->json([
            'status' => 'ok',
            'data' => $task,
        ], 200);
    }
}
