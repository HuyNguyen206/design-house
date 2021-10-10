<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class VerificationController extends Controller
{
    public $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserInterface $userRepository)
    {
//        $this->middleware('auth');
//        $this->middleware('signed')->only('verify');
        $this->userRepository = $userRepository;
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        if (!URL::hasValidSignature($request)) {
            return response()->error('Invalid verification link or signature', 500, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->error('Email address already verified', 500, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user->markEmailAsVerified();
        event(new Verified($user));

        return \response()->success([], 'Email successfully verified');
    }

    public function resend(Request $request)
    {
        $request->validate([
           'email' => 'email|required'
        ]);
        $user = $this->userRepository->findWhereFirst('email', $request->email);
//        $user = User::query()->whereEmail($request->email)->first();
        if (!$user) {
            return \response()->error('No user could be found with this email address');
        }

        if ($user->hasVerifiedEmail()) {
            return response()->error('Email address already verified', 500, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->sendEmailVerificationNotification();
        return \response()->success([], 'Verification email was resent successfully');

    }
}
