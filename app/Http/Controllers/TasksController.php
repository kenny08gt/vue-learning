<?php

namespace App\Http\Controllers;

use App\Tasks\Task;
use App\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'description' => 'nullable|max:255',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'pipeline_id' => 'nullable|int',
            'pipeline_position' => 'nullable|int',
            'user_id' => 'nullable|int'
        ]);

        if ($request->has('pipeline_position') && $request->has('pipeline_id')) {
            DB::statement('update tasks set pipeline_position = pipeline_position + 1 where pipeline_id = ? and pipeline_position >= ?', array($request->pipeline_id, $request->pipeline_position));
        }

        $task = Task::updateOrCreate(['id' => $request->get('id')], $request->except(['id', 'user_id']));

        if (!$request->has('pipeline_position') && !$request->has('id')) {
            $count = DB::select("select count(*) as count from tasks where pipeline_id = " . $task->pipeline_id);
            $task->pipeline_position = $count[0]->count;
            $task->save();
        }

        if ($request->has('user_id')) {
            $user = User::findOrFail($request->get('user_id'));
            $task->users()->attach($user);
            $task->save();
        }

        return response(['payload' => $task, 'status' => 'success', 'message' => 'Task created!'], 200);
    }

    public function get(Request $request, $id = null)
    {
        try {
            $user_id = $request->get('user_id');
            if ($id) {
                if ($user_id) {
                    $task = Task::join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
                        ->where('user_tasks.user_id', $user_id)
                        ->where('tasks.id', $id)
                        ->with('users')
                        ->first();
                } else {
                    $task = Task::where('id', $id)->with('users')->first();
                }
                return response(['payload' => collect($task)], 200);
            } else {
                if ($user_id) {
                    $tasks = Task::join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
                        ->where('user_tasks.user_id', $user_id)->with('users')->get();
                } else {
                    $tasks = Task::with('users')->get();
                }
                return response(['payload' => $tasks, 'user_id' => $user_id], 200);
            }

        } catch (\Exception $exception) {
            return response(['error' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], 500);
        }
    }

    public function getGanttTasks(Request $request)
    {
        $user_id = $request->get('user_id');
        $pipeline_id = $request->get('pipeline_id');
        $tasks_query = Task::whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->with('users')
            ->with('pipeline');
        $start_date_query = DB::table('tasks')
            ->select('start_date')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->orderBy('start_date', 'asc')
            ->limit(1);
        $due_date_query = DB::table('tasks')
            ->select('due_date')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'desc')
            ->limit(1);

        if ($user_id) {
            $tasks_query = $tasks_query->join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
                ->where('user_tasks.user_id', $user_id);
            $start_date_query = $start_date_query
                ->join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
                ->where('user_tasks.user_id', $user_id);
            $due_date_query = $due_date_query
                ->join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
                ->where('user_tasks.user_id', $user_id);
        }

        if ($pipeline_id) {
            $tasks_query = $tasks_query->where('tasks.pipeline_id', $pipeline_id);
            $start_date_query = $start_date_query->where('tasks.pipeline_id', $pipeline_id);
            $due_date_query = $due_date_query->where('tasks.pipeline_id', $pipeline_id);
        }

        $tasks = $tasks_query->get();
        $start_date = $start_date_query->get();
        $due_date = $due_date_query->get();


        $start_date = count($start_date) > 0 ? date('D M d Y H:i:s O', strtotime($start_date[0]->start_date)) : '';
        $due_date = count($due_date) > 0 ? date('D M d Y H:i:s O', strtotime($due_date[0]->due_date)) : '';

        return response(['tasks' => $tasks, 'start_date' => $start_date, 'due_date' => $due_date], 200);

    }
}
