<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class ResetPassword extends \Illuminate\Auth\Notifications\ResetPassword
{

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = urlencode($notifiable->email);
//        $url = Str::of(route('api.password.reset', ['token' => $this->token, 'email' => $email]))->replace(config('app.url'), config('app.client_url'))->__toString();
        $url = config('app.client_url')."/password/reset?token=$this->token&email=$email";
        return (new MailMessage)
                    ->line('You are receiving this email because we received as password reset request for your account.')
                    ->action('Reset password', $url)
                    ->line('If you did not request a password reset, no further action require ');
    }

}
