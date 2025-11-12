<?php

namespace App\Services;

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use Illuminate\Support\Arr;

class BrevoMailer
{
    /**
     * Send an email via the Brevo transactional API.
     *
     * @param  array{
     *     from_email: string,
     *     from_name?: string,
     *     to: array<int, array{email: string, name?: string}>,
     *     subject: string,
     *     html: string,
     *     text?: string|null,
     *     reply_to?: array{email: string, name?: string}|null
     * }  $payload
     */
    public function send(array $payload): void
    {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', config('services.brevo.api_key'));

        $apiInstance = new TransactionalEmailsApi(null, $config);

        $email = new SendSmtpEmail([
            'sender' => [
                'email' => $payload['from_email'],
                'name' => $payload['from_name'] ?? $payload['from_email'],
            ],
            'to' => array_map(
                fn (array $recipient) => [
                    'email' => $recipient['email'],
                    'name' => $recipient['name'] ?? $recipient['email'],
                ],
                $payload['to']
            ),
            'subject' => $payload['subject'],
            'htmlContent' => $payload['html'],
            'textContent' => $payload['text'] ?? null,
        ]);

        if ($replyTo = Arr::get($payload, 'reply_to')) {
            $email->setReplyTo([
                'email' => $replyTo['email'],
                'name' => $replyTo['name'] ?? $replyTo['email'],
            ]);
        }

        $apiInstance->sendTransacEmail($email);
    }
}

