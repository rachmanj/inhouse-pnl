<?php

namespace App\Jobs;

use App\Models\DeliveryLog;
use App\Models\ReportPackage;
use App\Services\Hermes\HermesClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverReportPackageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ReportPackage $reportPackage,
        public array $recipients = [],
    ) {}

    public function handle(HermesClient $hermes): void
    {
        $artifact = $this->reportPackage->artifacts()->latest()->first();

        foreach ($this->recipients as $recipient) {
            $log = DeliveryLog::create([
                'report_package_id' => $this->reportPackage->id,
                'channel' => 'email',
                'recipient' => $recipient,
                'artifact_hash' => $artifact?->file_hash ?? '',
                'status' => 'queued',
            ]);

            try {
                $hermes->sendEmail($recipient, 'Monthly Report', $artifact?->file_path);
                $log->update(['status' => 'sent', 'sent_at' => now()]);
            } catch (\Throwable $e) {
                $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            }
        }

        $this->reportPackage->update(['status' => 'delivered']);
    }
}
