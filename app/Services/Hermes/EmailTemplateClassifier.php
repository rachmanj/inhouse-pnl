<?php

namespace App\Services\Hermes;

use App\Models\EmailTemplate;
use Illuminate\Support\Str;

class EmailTemplateClassifier
{
    public function classify(array $payload): ?EmailTemplate
    {
        $sender = $payload['from'] ?? $payload['sender'] ?? '';
        $subject = $payload['subject'] ?? '';

        return EmailTemplate::all()->first(function (EmailTemplate $template) use ($sender, $subject) {
            if (! $this->matchesPattern($sender, $template->sender_pattern)) {
                return false;
            }

            if ($template->subject_pattern && ! $this->matchesPattern($subject, $template->subject_pattern)) {
                return false;
            }

            return true;
        });
    }

    private function matchesPattern(string $value, string $pattern): bool
    {
        if (Str::contains($pattern, '*')) {
            $regex = '/^'.str_replace('\*', '.*', preg_quote($pattern, '/')).'$/i';

            return (bool) preg_match($regex, $value);
        }

        return Str::contains(Str::lower($value), Str::lower($pattern));
    }
}
