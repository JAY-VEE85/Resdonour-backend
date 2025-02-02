<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;  // Import Str for generating random code

class VerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the verification mail message.
     */
    public function toMail($notifiable)
    {
        // Retrieve the stored verification code from the database
        $verificationCode = $notifiable->verification_code;

        // ayaw niya magpakita
        $logoUrl = asset('storage/logo/ResIt.png');

        return (new MailMessage)
            ->subject('Complete Your Registration – Verify Your Email')
            ->greeting('Welcome to Re’sIt!')
            ->line('We’re excited to have you join us! To complete your registration and start exploring, please use the following verification code:')
            ->line('Verification Code: **' . $verificationCode . '**')
            ->line('If you didn’t create an account, you can safely ignore this email. Your email address will not be used.')
            ->salutation('Best Regards, The Re’sIt Team')
            ->markdown('vendor.notifications.email', ['logoUrl' => $logoUrl, 'verificationCode' => $verificationCode]);
    }
}