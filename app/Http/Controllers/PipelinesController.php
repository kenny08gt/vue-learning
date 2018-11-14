<?php

namespace App\Http\Controllers;

use App\Pipelines\Pipeline;
use Illuminate\Http\Request;

class PipelinesController extends Controller
{
    public function get(Request $request)
    {
        $user_id = $request->get('user_id');
        if ($user_id) {
            $pipelines = Pipeline::with(['tasks' => function ($query) use ($user_id){
                $query->orderBy('pipeline_position', 'asc')
                    ->join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
                    ->where('user_tasks.user_id', $user_id)
                    ->with('users');
            }])->get();
        } else {
            $pipelines = Pipeline::with(['tasks' => function ($query) {
                $query->orderBy('pipeline_position', 'asc')
                    ->with('users');
            }])->get();
        }

        return response(['pipelines' => $pipelines], 200);
    }
}
