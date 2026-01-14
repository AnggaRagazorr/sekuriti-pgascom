<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;

class DocumentLogController extends Controller
{
    public function index()
    {
        return view('security.document-log.index');
    }
}
