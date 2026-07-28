<?php

namespace App\Jobs;

use App\Exceptions\Domain\PeriodLockedException;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Pnl\PnlAggregationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RecalculatePnlSnapshotJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ReportPeriod $reportPeriod,
        public ?ProjectSite $projectSite = null,
    ) {}

    public function handle(PnlAggregationService $aggregation): void
    {
        if ($this->reportPeriod->status === 'locked') {
            Log::warning('Skipped P&L recalculation for locked period', [
                'period_id' => $this->reportPeriod->id,
            ]);

            return;
        }

        try {
            if ($this->projectSite) {
                $aggregation->aggregateSite($this->reportPeriod, $this->projectSite);
            }

            $aggregation->aggregateConsolidated($this->reportPeriod);
        } catch (PeriodLockedException $e) {
            Log::warning($e->getMessage(), ['period_id' => $this->reportPeriod->id]);
        }
    }
}
