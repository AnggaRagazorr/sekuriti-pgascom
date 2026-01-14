<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\PatrolReport;
use Illuminate\Http\Request;

class PatrolReportController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'report_date' => ['required', 'date'],
            'report_day' => ['required', 'string', 'max:20'],
            'submitted_time' => ['required', 'date'],

            'situasi' => ['nullable', 'string'],
            'aght' => ['nullable', 'string'],
            'cuaca' => ['nullable', 'string', 'max:100'],
            'pdam' => ['nullable', 'string', 'max:100'],
            'personel_wfo' => ['required', 'string', 'max:100'],
            'personel_tambahan' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        // 1 report per hari per user (update kalau sudah ada)
        $report = PatrolReport::updateOrCreate(
            ['user_id' => $user->id, 'report_date' => $data['report_date']],
            [
                'report_day' => $data['report_day'],
                'submitted_time' => $data['submitted_time'],
                'situasi' => $data['situasi'] ?? null,
                'aght' => $data['aght'] ?? null,
                'cuaca' => $data['cuaca'] ?? null,
                'pdam' => $data['pdam'] ?? null,
                'personel_wfo' => $data['personel_wfo'],
                'personel_tambahan' => $data['personel_tambahan'] ?? null,
            ]
        );

        return response()->json([
            'ok' => true,
            'id' => $report->id,
        ]);
    }
}
