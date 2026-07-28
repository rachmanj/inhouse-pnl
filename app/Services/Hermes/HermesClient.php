<?php

namespace App\Services\Hermes;

class HermesClient
{
    public function sendEmail(string $recipient, string $subject, ?string $attachmentPath = null): void
    {
        // Hermes gateway integration placeholder
    }

    public function sendWhatsApp(string $recipient, string $message): void
    {
    }

    public function sendTelegram(string $recipient, string $message): void
    {
    }
}
