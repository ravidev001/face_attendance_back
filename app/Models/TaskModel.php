<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $fillable = [
        'title',
        'description',
        'is_completed',
        'created_at',
        'updated_at'
    ];
}
