<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueryPerformanceLog extends Model
{
    protected $fillable = ['query', 'execution_time', 'rows_examined', 'key_used'];
}
