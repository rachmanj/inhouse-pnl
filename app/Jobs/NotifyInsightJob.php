<?php

namespace App\Jobs;

use App\Services\Hermes\HermesClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyInsightJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $message,
    ) {}

    public function handle(HermesClient $hermes): void
    {
        $whatsapp = config('services.hermes.default_whatsapp_recipient');
        $telegram = config('services.hermes.default_telegram_chat_id');

        $prefix = match ($this->type) {
            'anomaly' => '⚠️ Anomaly: ',
            'reconciliation_failed' => '🔴 Reconciliation: ',
            default => 'ℹ️ Insight: ',
        };

        $body = $prefix.$this->message;

        if ($whatsapp) {
            $hermes->sendWhatsApp($whatsapp, $body);
        }

        if ($telegram) {
            $hermes->sendTelegram($telegram, $body);
        }
    }
}
