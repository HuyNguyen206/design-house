<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    protected function attemptLogin(Request $request)
    {
        //Attemp to issue a token to the user
        $token = $this->guard()->attempt($this->credentials($request));
        if (!$token) {
            return false;
        }
        //Get authenticated user
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return false;
        }
        //Set user's token
        $this->guard()->setToken($token);
        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);
        //Get the token from authentication guard JWT
        $token = (string)$this->guard()->getToken();

        //Extract the expiry date
        $expiration = $this->guard()->getPayLoad()->get('exp');

        return response()->success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return response()->error('You need to verify your email account');
        }

        throw ValidationException::withMessages([
            $this->username() => 'Invalid credential'
        ]);

    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        return response()->success([], 'Logged out successfully!', 200, Response::HTTP_NO_CONTENT);
    }

}
