<?php

namespace App\Http\Controllers\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\AnomalyAlert;
use App\Models\ReconciliationCheck;
use App\Models\ReportPeriod;
use App\Models\VarianceFlag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InsightsController extends Controller
{
    public function index(Request $request): Response
    {
        $period = $this->resolvePeriod($request);

        return Inertia::render('Intelligence/Insights', [
            'period' => $period,
            'insights' => $this->buildFeed($period),
        ]);
    }

    private function resolvePeriod(Request $request): ReportPeriod
    {
        if ($request->filled('period_id')) {
            return ReportPeriod::findOrFail($request->integer('period_id'));
        }

        return ReportPeriod::orderByDesc('year')->orderByDesc('month')->firstOrFail();
    }

    private function buildFeed(ReportPeriod $period): array
    {
        $variances = VarianceFlag::with(['projectSite', 'pnlLine'])
            ->where('report_period_id', $period->id)
            ->whereIn('severity', ['warning', 'critical'])
            ->where('is_acknowledged', false)
            ->get()
            ->map(fn ($f) => [
                'type' => 'variance',
                'severity' => $f->severity,
                'title' => ($f->pnlLine?->name ?? 'Variance').' — '.$f->projectSite?->code,
                'message' => sprintf('%s: %+.2f%%', strtoupper($f->comparison_type), $f->delta_percent),
                'created_at' => $f->created_at?->toIso8601String(),
            ]);

        $anomalies = AnomalyAlert::with(['projectSite', 'account'])
            ->where('report_period_id', $period->id)
            ->where('status', 'open')
            ->get()
            ->map(fn ($a) => [
                'type' => 'anomaly',
                'severity' => 'critical',
                'title' => $a->metric.' — '.$a->projectSite?->code,
                'message' => $a->explanation,
                'created_at' => $a->created_at?->toIso8601String(),
            ]);

        $reconciliations = ReconciliationCheck::whereHas('importBatch', fn ($q) => $q->where('report_period_id', $period->id))
            ->where('is_reconciled', false)
            ->get()
            ->map(fn ($r) => [
                'type' => 'reconciliation',
                'severity' => 'warning',
                'title' => 'Unreconciled import batch #'.$r->import_batch_id,
                'message' => 'Discrepancy: '.number_format((float) $r->discrepancy, 2),
                'created_at' => $r->created_at?->toIso8601String(),
            ]);

        return $variances->concat($anomalies)->concat($reconciliations)
            ->sortByDesc('created_at')
            ->values()
            ->all();
    }
}
