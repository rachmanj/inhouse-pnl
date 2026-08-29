<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\ImportBatch;
use App\Models\ProjectSite;
use App\Models\ReportPeriod;
use App\Services\Hermes\EmailTemplateClassifier;
use App\Services\Hermes\PettyCashEmailParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HermesInboundController extends Controller
{
    public function handle(Request $request, EmailTemplateClassifier $classifier, PettyCashEmailParser $parser): JsonResponse
    {
        $payload = $request->all();
        $template = $classifier->classify($payload);

        if (! $template) {
            $period = ReportPeriod::orderByDesc('year')->orderByDesc('month')->first();

            $batch = ImportBatch::create([
                'report_period_id' => $period?->id ?? 1,
                'source' => 'email',
                'status' => 'pending',
                'original_filename' => $payload['subject'] ?? 'email-inbound',
                'triggered_by' => null,
                'started_at' => now(),
            ]);

            return response()->json(['status' => 'staged', 'import_batch_id' => $batch->id]);
        }

        if ($template->target === 'petty_cash') {
            $period = ReportPeriod::orderByDesc('year')->orderByDesc('month')->firstOrFail();
            $siteCode = $payload['site_code'] ?? 'HO';
            $site = ProjectSite::where('code', $siteCode)->firstOrFail();

            $expenses = $parser->parse($payload, $template, $period, $site->id);

            return response()->json([
                'status' => 'parsed',
                'target' => 'petty_cash',
                'count' => count($expenses),
            ]);
        }

        return response()->json(['status' => 'ignored', 'target' => $template->target]);
    }
}
