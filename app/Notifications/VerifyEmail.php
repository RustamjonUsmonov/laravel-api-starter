<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', $url)
            ->line('If you did not create an account, no further action is required.');
    }

    protected function verificationUrl($notifiable): string
    {
        $frontendUrl = config('app.frontend_url');
        $params = [
            'id' => $notifiable->getKey(),
            'hash' => sha1((string) $notifiable->getEmailForVerification()),
        ];

        return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                $params,
                absolute: false
            ) . $frontendUrl . '/verify-email/' . $params['id'] . '/' . $params['hash'];
    }
}
