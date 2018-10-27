<?php

namespace App\Tasks;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';
    protected $guarded = [];

    public function pipeline()
    {
        return $this->belongsTo('App\Pipelines\Pipeline','pipeline_id','id', '');
    }
}
