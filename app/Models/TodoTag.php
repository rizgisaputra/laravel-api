<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoTag extends Model
{
    use HasFactory;

    protected $table = 'todos_tags';
    protected $guarded = [];
}
