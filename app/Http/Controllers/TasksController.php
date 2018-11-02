<?php

namespace App\Http\Controllers;

use App\Tasks\Task;
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
            'pipeline_position' => 'nullable|int'
        ]);

        if ($request->has('pipeline_position') && $request->has('pipeline_id')) {
            DB::statement('update tasks set pipeline_position = pipeline_position + 1 where pipeline_id = ? and pipeline_position >= ?', array($request->pipeline_id, $request->pipeline_position));
        }

        $task = Task::updateOrCreate(['id' => $request->get('id')], $request->except('id'));

        if (!$request->has('pipeline_position') && !$request->has('id')) {
            $count = DB::select("select count(*) as count from tasks where pipeline_id = " . $task->pipeline_id);
            $task->pipeline_position = $count[0]->count;
            $task->save();
        }

        return response(['payload' => $task, 'status' => 'success', 'message' => 'Task created!'], 200);
    }

    public function get(Request $request, $id = null)
    {
        try {
            if ($id)
                return response(['payload' => collect(Task::findOrFail($id))], 200);
            else
                return response(['payload' => Task::all()], 200);

        } catch (\Exception $exception) {
            return response(['error' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()], 500);
        }
    }

    public function getGanttTasks(Request $request)
    {
        $tasks = Task::whereNotNull('start_date')->whereNotNull('due_date')->get();
        $start_date = DB::table('tasks')
            ->select('start_date')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->orderBy('start_date', 'asc')
            ->limit(1)
            ->get();
        $due_date = DB::table('tasks')
            ->select('due_date')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'desc')
            ->limit(1)
            ->get();

        $start_date = count($start_date) > 0 ? date('D M d Y H:i:s O', strtotime($start_date[0]->start_date)) : '';
        $due_date = count($due_date) > 0 ? date('D M d Y H:i:s O', strtotime($due_date[0]->due_date)) : '';

        return response(['tasks' => $tasks, 'start_date' => $start_date, 'due_date' => $due_date], 200);

    }
}
