<?php

namespace App\Notifications\Channels;

use App\Services\BrevoMailer;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class BrevoChannel
{
    public function __construct(private BrevoMailer $mailer)
    {
    }

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toMail')) {
            return;
        }

        $mailMessage = $notification->toMail($notifiable);

        $from = $mailMessage->from ?? [
            config('mail.from.address'),
            config('mail.from.name'),
        ];

        $replyTo = $mailMessage->replyTo[0] ?? null;

        $html = $mailMessage->render();
        $text = method_exists($mailMessage, 'renderText')
            ? $mailMessage->renderText()
            : trim((string) Str::of($html)->stripTags());

        $this->mailer->send([
            'from_email' => $from[0] ?? config('mail.from.address'),
            'from_name' => $from[1] ?? config('mail.from.name'),
            'to' => [
                [
                    'email' => $notifiable->routeNotificationFor('mail'),
                    'name' => $notifiable->name ?? $notifiable->email ?? '',
                ],
            ],
            'subject' => $mailMessage->subject ?? '',
            'html' => $html instanceof HtmlString ? $html->toHtml() : $html,
            'text' => $text,
            'reply_to' => $replyTo
                ? ['email' => $replyTo[0], 'name' => $replyTo[1] ?? $replyTo[0]]
                : null,
        ]);
    }
}

