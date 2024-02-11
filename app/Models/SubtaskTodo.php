<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubtaskTodo extends Model
{
    use HasFactory;
    protected $table = 'subtask_todos';
    protected $guarded = [];

}
