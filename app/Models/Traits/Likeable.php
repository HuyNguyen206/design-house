<?php
namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Support\Str;

trait Likeable
{
    public function likedUsers()
    {
        return $this->morphToMany(User::class, 'likeable')->withPivot('id')->withTimestamps();
    }

}
