<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Rules\MatchOldPassword;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    //
    public function updateProfile()
    {
        $data = \request()->validate([
            'name' => 'required',
            'tag_line' => 'string',
            'about' => 'string|min:20',
            'location.latitude' => 'required|numeric|min:-90|max:90',
            'location.longitude' => 'required|numeric|min:-180|max:180',
            'formatted_address' => 'string',
            'available_to_hire' => 'boolean'
        ]);
        $data['location'] = new Point($data['location']['latitude'], $data['location']['longitude']);
        $user = Auth::user();
        $user->update($data);
        return \response()->success(new UserResource($user), 'Update profile successfully');
    }

    public function updatePassword()
    {
        $user = Auth::user();
        $data = $this->validate(\request(), [
//            'current_password' => ['required', new MatchOldPassword($user)],
            'current_password' => ['required', 'current_password', 'different:password'],
            'password' => ['required','confirmed']
        ]);
        $data['password'] = bcrypt($data['password']);
        $user->update($data);
        return response()->success(new UserResource($user), 'Update password successfully');
    }
}
