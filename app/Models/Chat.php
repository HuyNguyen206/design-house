<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants')->withTimestamps();
    }

    public function messageUsers()
    {
        return $this->belongsToMany(User::class, 'messages')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

//    public function inUnreadForUser($userId)
//    {
//        return $this->messages()->where('participants.user_id', $userId)->first()->last_read_at;
//
//    }

    public function inUnreadForUser($userId)
    {
        return $this->messages()->where('user_id', '<>', $userId)->whereNull('read_at')->exists();
    }

    public function markAsReadForUser($userId)
    {
        $this->messages()->where('user_id', '<>', $userId)->whereNull('read_at')->update([
           'read_at' => Carbon::now()
        ]);
    }

}
