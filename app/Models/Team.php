<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($team) {
            $team->members()->attach(auth()->id());
        });

        static::deleting(function ($team) {
            $team->members()->detach();
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function hasUser($userId)
    {
        return $this->members()->where('id', $userId)->exists();
    }

    public function sendInviteUsers()
    {
        return $this->belongsToMany(User::class, 'invitations', 'team_id', 'sender_id')->withPivot(['recipient_email', 'token']);
    }

    public function hasPendingInviteEmail($email)
    {
        return $this->sendInviteUsers()->where('invitations.recipient_email', $email)->exists();
    }
}
