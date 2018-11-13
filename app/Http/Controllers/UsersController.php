<?php

namespace App\Http\Controllers;

use App\Users\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function get()
    {
        $users = User::with('tasks')->get();

        return response(['users' => $users], 200);
    }
}
