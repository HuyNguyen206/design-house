<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\Criteria\FilterByWhereField;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public $teamRepo, $userRepo;

    public function __construct(TeamInterface $teamRepo, UserInterface $userRepo)
    {
        $this->teamRepo = $teamRepo;
        $this->userRepo = $userRepo;
    }

    //
    public function sendInvitation($teamId)
    {
        $team = $this->teamRepo->find($teamId);
        $data = \request()->validate([
            'recipient_email' => 'required|email'
        ]);
        $user = auth()->user();
        // Check if user own the team
        if (!$user->isOwnerOfTeam($teamId)) {
            return response()->error('You are not owner of this team', 403, 403);
        }
        // Check if the invitation email already send to this recipient_email
        if ($team->hasPendingInviteEmail($data['recipient_email'])) {
            return response()->error("This email {$data['recipient_email']} was already invite to the team {$team->name}", 400, 400);
        }

        $recipientUser = $this->userRepo->withCriteria([
            new FilterByWhereField('email', $data['recipient_email'])
        ])->first();
        $isRegisterUser = $recipientUser;

        //If $recipientUser exist and already join the team
        if ($isRegisterUser && $team->members()->where('users.id', $recipientUser->id)->exists()) {
            return response()->error("The user with email $recipientUser->email already join the team $team->name", 400, 400);
        }

        //Send invite email to user
        $token = md5(uniqid(microtime(), true));
        $team->sendInviteUsers()->attach($user->id, [
            'recipient_email' => $data['recipient_email'],
            'token' => $token
        ]);
        $invitation = [
            'recipient_email' => $data['recipient_email'],
            'token' => $token,
            'sender' => $user,
            'team' => $team
        ];
        Mail::to($data['recipient_email'])->send(new SendInvitationToJoinTeam($invitation, $isRegisterUser));
        return response()->success([], 'Invitation was sent to user');

    }

    public function resend($id)
    {
        $invitation = DB::table('invitations')->where('id', $id)->first();
        if(!$invitation) {
            throw new ModelNotFoundException("No query results for model Invitation $id");
        }
        $user = auth()->user();
        // Check if user own the team
        if (!$user->isOwnerOfTeam($invitation->team_id)) {
            return response()->error('You are not owner of this team', 403, 403);
        }

        $recipientUser = $this->userRepo->withCriteria([
            new FilterByWhereField('email', $invitation->recipient_email)
        ])->first();
        $isRegisterUser = $recipientUser;

        $data = [
            'recipient_email' => $invitation->recipient_email,
            'team' => $this->teamRepo->find($invitation->team_id)
        ];
        Mail::to($invitation->recipient_email)->send(new SendInvitationToJoinTeam($data, $isRegisterUser));
        return response()->success([], 'Invitation was resent to user');
    }

    public function respond($id)
    {
        $invitationQuery = DB::table('invitations')->where('id', $id);
        if (!$invitation = $invitationQuery->first()) {
            throw new ModelNotFoundException("No query results for model Invitation $id");
        }

       $data = \request()->validate([
           'token' => 'required',
           'is_accept' => 'required|boolean'
        ]);
        $user = auth()->user();
        $this->authorize('respond-invitation', $invitation);

        //Check to make sure the token is match
        if ($data['token'] !== $invitation->token) {
            return response()->error('The token is mismatch', 400, 400);
        }

        if ($data['is_accept']) {
            $team = $this->teamRepo->find($invitation->team_id);
            $team->members()->attach($user->id);
        }
        $invitationQuery->delete();
        $message = $data['is_accept'] ? "Accept successfully" : "Reject successfully";
        return response()->success([], $message);

    }

    public function deleteInvitation($id)
    {
        $invitationQuery = DB::table('invitations')->where('id', $id);
        if (!$invitation = $invitationQuery->first()) {
            throw new ModelNotFoundException("No query results for model Invitation $id");
        }
        $this->authorize('delete-invitation', $invitation);
        $invitationQuery->delete();
        return response()->success([], 'Delete successfully');
    }

}
