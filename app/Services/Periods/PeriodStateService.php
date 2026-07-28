<?php

namespace App\Services\Periods;

use App\Enums\ReportPeriodStatus;
use App\Exceptions\Domain\InvalidPeriodTransitionException;
use App\Exceptions\Domain\UnreconciledPeriodException;
use App\Models\AccountBalance;
use App\Models\ImportBatch;
use App\Models\PnlSnapshot;
use App\Models\ReconciliationCheck;
use App\Models\ReportPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PeriodStateService
{
    private const TRANSITIONS = [
        'open' => ['in_review'],
        'in_review' => ['open', 'approved'],
        'approved' => ['delivered'],
        'delivered' => ['locked'],
        'locked' => ['in_review'],
    ];

    public function transition(ReportPeriod $period, ReportPeriodStatus $to, User $actor): void
    {
        $from = ReportPeriodStatus::from($period->status);
        $allowed = self::TRANSITIONS[$from->value] ?? [];

        if (! in_array($to->value, $allowed, true)) {
            $this->audit($actor, 'period.transition_blocked', $period, ['from' => $from->value, 'to' => $to->value]);
            throw new InvalidPeriodTransitionException($from->value, $to->value);
        }

        if ($from === ReportPeriodStatus::Open && $to === ReportPeriodStatus::InReview) {
            $this->assertReconciled($period);
        }

        if ($from === ReportPeriodStatus::Locked && $to === ReportPeriodStatus::InReview) {
            if (! $actor->can('periods.lock')) {
                throw new InvalidPeriodTransitionException($from->value, $to->value);
            }
        }

        DB::transaction(function () use ($period, $to, $actor, $from) {
            $period->update(['status' => $to->value]);

            if ($to === ReportPeriodStatus::Locked) {
                $period->update([
                    'locked_at' => now(),
                    'locked_by' => $actor->id,
                ]);

                AccountBalance::where('report_period_id', $period->id)
                    ->update(['is_locked' => true]);

                PnlSnapshot::where('report_period_id', $period->id)
                    ->update(['status' => 'final']);
            }

            if ($from === ReportPeriodStatus::Locked && $to === ReportPeriodStatus::InReview) {
                $period->update(['locked_at' => null, 'locked_by' => null]);
                AccountBalance::where('report_period_id', $period->id)
                    ->update(['is_locked' => false]);
            }

            $this->audit($actor, 'period.'.$to->value, $period, ['from' => $from->value]);
        });
    }

    private function assertReconciled(ReportPeriod $period): void
    {
        $batchIds = ImportBatch::where('report_period_id', $period->id)
            ->where('status', 'completed')
            ->pluck('id');

        if ($batchIds->isEmpty()) {
            return;
        }

        $unreconciled = ReconciliationCheck::whereIn('import_batch_id', $batchIds)
            ->where('is_reconciled', false)
            ->exists();

        if ($unreconciled) {
            throw new UnreconciledPeriodException;
        }
    }

    private function audit(User $actor, string $action, ReportPeriod $period, array $changes = []): void
    {
        DB::table('audit_logs')->insert([
            'user_id' => $actor->id,
            'action' => $action,
            'auditable_type' => ReportPeriod::class,
            'auditable_id' => $period->id,
            'changes' => json_encode($changes),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
