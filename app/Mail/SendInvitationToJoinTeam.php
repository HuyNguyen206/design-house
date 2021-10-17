<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvitationToJoinTeam extends Mailable
{
    use Queueable, SerializesModels;
    public $invitation, $isRegisterUser;
    /**
     * Create a new message instance.
     *
     * @param $invitation
     * @param $isRegisterUser
     */
    public function __construct($invitation, $isRegisterUser)
    {
        $this->invitation = $invitation;
        $this->isRegisterUser = $isRegisterUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->isRegisterUser) {
            $dashBoardLink = config('app.client_url')."/setting/team";
            return $this->markdown('emails.invitation.invite-register-user')
                ->subject("Invitation to join team {$this->invitation['team']->name}")
                ->with([
                    'invitation' => $this->invitation,
                    'dashBoardLink' => $dashBoardLink
                ]);
        }

        $registerLink = config('app.client_url')."/register?invitation={$this->invitation['recipient_email']}";
        return  $this->markdown('emails.invitation.invite-new-user')
        ->subject("Invitation to join team {$this->invitation['team']->name}")
        ->with([
            'invitation' => $this->invitation,
            'registerLink' => $registerLink
        ]);

    }
}
