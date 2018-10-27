<?php

namespace App\Pipelines;

use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    protected $guarded = [];
    protected $table = 'pipelines';

    public function tasks()
    {
        return $this->hasMany('App\Tasks\Task','pipeline_id','id');
    }
}
