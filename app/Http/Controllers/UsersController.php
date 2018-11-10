<?php

namespace App\Http\Controllers;

use App\Users\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function get()
    {
        $users = User::all();

        return response(['users' => $users], 200);
    }
}
