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
        ]);

        Task::updateOrCreate(['id' => $request->get('id')], $request->except('id'));

        return response(['payload' => '', 'status' => 'success', 'message' => 'Task created!'], 200);
    }

    public function get(Request $request)
    {
        return response(['payload' => Task::all()], 200);
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
