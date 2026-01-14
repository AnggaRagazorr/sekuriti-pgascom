<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;

class CarpoolController extends Controller
{
    public function index()
    {
        return view('security.carpool.index');
    }
}
