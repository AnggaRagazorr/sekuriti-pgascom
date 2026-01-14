<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;

class PatrolController extends Controller
{
    public function index()
    {
        return view('security.patrol.index');
    }
}
