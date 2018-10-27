<?php

namespace App\Http\Controllers;

use App\Pipelines\Pipeline;
use Illuminate\Http\Request;

class PipelinesController extends Controller
{
    public function get(Request $request)
    {
        $pipelines = Pipeline::with('tasks')->get();

        return response(['pipelines' => $pipelines], 200);
    }
}
