@component('mail::message')
# Hi {{$invitation['recipient_email']}},

You have been invited to join the team
**{{ $invitation['team']->name }}.
Because you are not yet to signed up to the platform, please
[Register for free]({{$registerLink}}), then you can access or reject the
invitation in your team management console.



@component('mail::button', ['url' => $registerLink])
Register for free
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
