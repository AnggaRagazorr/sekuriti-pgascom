<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;

class DailyReportController extends Controller
{
    public function index()
    {
        return view('security.daily-report.index');
    }
}
