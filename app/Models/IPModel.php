<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IPModel extends Model
{
    protected $table = 'ips';
    
    protected $fillable = [
        'ip',
        'created_at',
        'updated_at'
    ];
}
