<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Report;
use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        if (!Auth::user()->is_moderator) {
            return response([
                'message' => 'Mod yetkisi gerekli.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        $reports = Report::with(['reportable', 'reporter:id,name,username'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return response([
            'data' => $reports,
            'status' => true,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:title,entry',
            'id' => 'required|integer',
            'reason' => 'nullable|string|max:2000',
        ]);

        $reportable = $data['type'] === 'title'
            ? Title::findOrFail($data['id'])
            : Entry::findOrFail($data['id']);

        $report = Report::create([
            'reported_by' => Auth::id(),
            'reason' => $data['reason'],
            'reportable_id' => $reportable->id,
            'reportable_type' => get_class($reportable),
        ]);

        return response([
            'data' => $report,
            'status' => true,
            'message' => 'Rapor alındı.',
        ], Response::HTTP_CREATED);
    }

    public function resolve(Report $report, Request $request)
    {
        if (!Auth::user()->is_moderator) {
            return response([
                'message' => 'Mod yetkisi gerekli.',
                'status' => false
            ], Response::HTTP_FORBIDDEN);
        }

        $data = $request->validate([
            'resolution' => 'nullable|string|max:2000',
            'status' => 'required|in:resolved,ignored',
        ]);

        $report->update([
            'status' => $data['status'],
            'resolution' => $data['resolution'] ?? null,
            'resolved_by' => Auth::id(),
        ]);

        return response([
            'data' => $report,
            'status' => true,
            'message' => 'Rapor güncellendi.',
        ], Response::HTTP_OK);
    }
}
