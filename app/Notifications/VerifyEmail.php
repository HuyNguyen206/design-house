<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class VerifyEmail extends VerifyEmailNotification
{
  protected function verificationUrl($notifiable)
  {
    $appUrl = config('app.client_url', config('app.url'));
    $url = URL::temporarySignedRoute(
        'verification.verify',
        Carbon::now()->addMinutes(60),
        ['user' => $notifiable->id]
    );
    return Str::of($url)->replace(URL::to('/api'), $appUrl)->__toString();
  }
}
