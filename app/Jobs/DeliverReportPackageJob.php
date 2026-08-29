<?php

namespace App\Jobs;

use App\Enums\ReportPeriodStatus;
use App\Models\AnomalyAlert;
use App\Models\DeliveryLog;
use App\Models\ReportPackage;
use App\Models\VarianceFlag;
use App\Services\Hermes\HermesClient;
use App\Services\Periods\PeriodStateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class DeliverReportPackageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ReportPackage $package,
        public string $channel = 'email',
        public ?string $recipient = null,
    ) {}

    public function handle(HermesClient $hermes, PeriodStateService $periodState): void
    {
        $package = $this->package->load(['reportPeriod', 'artifacts', 'createdBy']);
        $period = $package->reportPeriod;

        if ($period->status !== 'approved') {
            return;
        }

        $openFlags = VarianceFlag::where('report_period_id', $period->id)
            ->where('is_acknowledged', false)
            ->whereIn('severity', ['warning', 'critical'])
            ->exists();

        $openAnomalies = AnomalyAlert::where('report_period_id', $period->id)
            ->where('status', 'open')
            ->exists();

        if ($openFlags || $openAnomalies) {
            return;
        }

        $artifact = $package->artifacts()->where('type', 'excel')->latest()->first()
            ?? $package->artifacts()->latest()->first();

        if (! $artifact) {
            return;
        }

        $recipient = $this->recipient ?? match ($this->channel) {
            'whatsapp' => config('services.hermes.default_whatsapp_recipient'),
            'telegram' => config('services.hermes.default_telegram_chat_id'),
            default => config('services.hermes.default_email_recipient'),
        };

        $log = DeliveryLog::create([
            'report_package_id' => $package->id,
            'channel' => $this->channel,
            'recipient' => $recipient,
            'artifact_hash' => $artifact->file_hash,
            'status' => 'queued',
        ]);

        $filePath = Storage::disk('local')->path($artifact->file_path);
        $subject = sprintf('ArkaLedger Report %d-%02d', $period->year, $period->month);

        try {
            $result = match ($this->channel) {
                'whatsapp' => $hermes->sendWhatsApp($recipient, $subject),
                'telegram' => $hermes->sendTelegram($recipient, $subject),
                default => $hermes->sendEmail($recipient, $subject, 'Monthly report attached.', $filePath),
            };

            $log->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'sent_at' => $result['success'] ? now() : null,
                'error_message' => $result['success'] ? null : json_encode($result['body']),
            ]);

            if ($result['success']) {
                $package->update(['status' => 'delivered']);
                $actor = $package->createdBy;
                if ($actor) {
                    $periodState->transition($period, ReportPeriodStatus::Delivered, $actor);
                }
            }
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
