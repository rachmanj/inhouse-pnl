<?php

namespace App\Services\Hermes;

use Illuminate\Support\Facades\Http;

class HermesClient
{
    public function sendEmail(string $recipient, string $subject, string $body, ?string $attachmentPath = null): array
    {
        return $this->post('/api/send/email', [
            'to' => $recipient,
            'subject' => $subject,
            'body' => $body,
            'attachment' => $attachmentPath,
        ]);
    }

    public function sendWhatsApp(string $recipient, string $message): array
    {
        return $this->post('/api/send/whatsapp', [
            'to' => $recipient,
            'message' => $message,
        ]);
    }

    public function sendTelegram(string $chatId, string $message): array
    {
        return $this->post('/api/send/telegram', [
            'chat_id' => $chatId,
            'message' => $message,
        ]);
    }

    private function post(string $path, array $payload): array
    {
        $response = Http::withToken(config('services.hermes.token'))
            ->baseUrl(config('services.hermes.base_url'))
            ->post($path, $payload);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json() ?? [],
        ];
    }
}
