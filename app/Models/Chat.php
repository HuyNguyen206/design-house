<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    public function messageUsers()
    {
        return $this->belongsToMany(User::class, 'messages');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function inUnreadForUser($userId)
    {
        return $this->messages()->where('participants.user_id', $userId)->first()->last_read_at;

    }

}
