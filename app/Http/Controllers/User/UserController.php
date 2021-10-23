<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public $userRepository;
    public function __construct(UserInterface $userRepository)
    {
      $this->userRepository  = $userRepository;
    }

    //
    public function index()
    {
        $users = $this->userRepository->paginate();
        return response()->success(UserResource::collection($users)->response()->getData());
    }

    public function likeToggleAction($id, $type)
    {
        $type = Str::of($type)->ucfirst()->plural();
        $user = auth()->user();
        $action = $user->isLike($id, $type) ? 'Unlike' : 'Like';

        $user->likeToggle($id, Str::ucfirst($type));
        return response()->success([], "$action $type successfully");
    }

    public function searchDesigner()
    {
        $designers = $this->userRepository->search(\request()->all());
        return response()->success(UserResource::collection($designers));
    }
}
