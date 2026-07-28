<?php

namespace App\Services\Periods;

use App\Enums\ReportPeriodStatus;
use App\Exceptions\Domain\InvalidPeriodTransitionException;
use App\Models\AccountBalance;
use App\Models\PnlSnapshot;
use App\Models\ReportPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PeriodStateService
{
    private array $allowed = [
        'open' => ['in_review'],
        'in_review' => ['open', 'approved'],
        'approved' => ['delivered'],
        'delivered' => ['locked'],
        'locked' => ['in_review'],
    ];

    public function transition(ReportPeriod $period, ReportPeriodStatus $to, User $actor): void
    {
        $from = $period->status;

        if (! in_array($to->value, $this->allowed[$from] ?? [])) {
            throw new InvalidPeriodTransitionException("Cannot transition from {$from} to {$to->value}");
        }

        DB::transaction(function () use ($period, $to, $actor) {
            $period->update(['status' => $to->value]);

            if ($to === ReportPeriodStatus::Locked) {
                $period->update(['locked_at' => now(), 'locked_by' => $actor->id]);
                AccountBalance::where('report_period_id', $period->id)->update(['is_locked' => true]);
                PnlSnapshot::where('report_period_id', $period->id)->update(['status' => 'final']);
            }
        });
    }
}
