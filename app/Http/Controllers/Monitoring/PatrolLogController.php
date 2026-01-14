<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\PatrolSubmission;
use App\Models\User;
use Illuminate\Http\Request;

class PatrolLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $userId = $request->get('user_id');
        
        $query = PatrolSubmission::with('user')
            ->whereDate('submitted_at', $date);

            if ($userId) {
                $query->where('user_id', $userId);
            }

       $rows = $query->orderByDesc('submitted_at')
            ->paginate(20)
            ->withQueryString();
            
            $users = User::whereHas('patrolSubmissions')
                ->orderBy('name')
                ->get(['id', 'name']);
       
            return view('monitoring.patrols.index', compact('rows', 'date', 'userId', 'users'));
    }

    public function show(PatrolSubmission $patrolSubmission)
    {
        $patrolSubmission->load('user');

        return view('monitoring.patrols.show', [
            'row' => $patrolSubmission
        ]);
    }
}
