<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class MeController extends Controller
{
    //
    public function getMe()
    {
        $user = auth()->user();
        return response()->success($user ? new UserResource($user) : null);
    }
}
