<?php

namespace App\Http\Controllers\Reports;

use App\Actions\Reports\ApproveApprovalStepAction;
use App\Actions\Reports\RejectApprovalStepAction;
use App\Http\Controllers\Controller;
use App\Models\ApprovalStep;
use App\Models\ReportPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApprovalStepController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.approve');
    }

    public function approve(ReportPackage $reportPackage, ApprovalStep $approvalStep, ApproveApprovalStepAction $action): RedirectResponse
    {
        abort_unless($approvalStep->report_package_id === $reportPackage->id, 404);

        $action->execute($approvalStep, request()->user());

        return back()->with('success', 'Approval step approved.');
    }

    public function reject(Request $request, ReportPackage $reportPackage, ApprovalStep $approvalStep, RejectApprovalStepAction $action): RedirectResponse
    {
        abort_unless($approvalStep->report_package_id === $reportPackage->id, 404);

        $validated = $request->validate([
            'comments' => ['required', 'string', 'min:3'],
        ]);

        $action->execute($approvalStep, $request->user(), $validated['comments']);

        return back()->with('success', 'Approval step rejected.');
    }
}
