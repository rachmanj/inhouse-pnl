<?php

namespace App\Actions\Reports;

use App\Enums\ReportPeriodStatus;
use App\Models\ApprovalStep;
use App\Models\ReportPackage;
use App\Models\User;
use App\Services\Periods\PeriodStateService;
use Illuminate\Support\Facades\DB;

class ApproveApprovalStepAction
{
    public function __construct(
        private PeriodStateService $periodState,
    ) {}

    public function execute(ApprovalStep $step, User $actor): void
    {
        DB::transaction(function () use ($step, $actor) {
            $step->update([
                'status' => 'approved',
                'acted_by' => $actor->id,
                'acted_at' => now(),
            ]);

            $package = $step->reportPackage;
            $pending = $package->approvalSteps()->where('status', 'pending')->count();

            if ($pending === 0) {
                $package->update(['status' => 'approved']);
                $this->periodState->transition(
                    $package->reportPeriod,
                    ReportPeriodStatus::Approved,
                    $actor
                );
            } else {
                $package->update(['status' => 'in_review']);
            }
        });
    }
}
