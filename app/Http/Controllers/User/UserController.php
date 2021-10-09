<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;

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
}
