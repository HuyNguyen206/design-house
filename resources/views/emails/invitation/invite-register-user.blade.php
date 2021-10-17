@component('mail::message')
# Hi {{$invitation['recipient_email']}},

You have been invited to join the team
**{{$invitation['team']->name}}.
Because you are already registerd to the platform, you just need to accept or reject invitation
in your
[Team management console]({{$dashBoardLink}}).



@component('mail::button', ['url' => $dashBoardLink])
Go to dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
