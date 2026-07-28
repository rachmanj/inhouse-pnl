<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\ApprovalStep;
use App\Models\ReportPackage;
use Illuminate\Http\Request;

class ApprovalStepController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.approve');
    }

    public function approve(ReportPackage $reportPackage, ApprovalStep $approvalStep)
    {
        $approvalStep->update([
            'status' => 'approved',
            'acted_by' => auth()->id(),
            'acted_at' => now(),
        ]);

        $pending = $reportPackage->approvalSteps()->where('status', 'pending')->count();
        if ($pending === 0) {
            $reportPackage->update(['status' => 'approved']);
        }

        return back()->with('success', 'Step approved.');
    }

    public function reject(Request $request, ReportPackage $reportPackage, ApprovalStep $approvalStep)
    {
        $request->validate(['comments' => 'required|string']);

        $approvalStep->update([
            'status' => 'rejected',
            'acted_by' => auth()->id(),
            'acted_at' => now(),
            'comments' => $request->comments,
        ]);

        $reportPackage->update(['status' => 'draft']);

        return back()->with('success', 'Step rejected.');
    }
}
