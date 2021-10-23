<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $touches = ['chat'];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBodyAttribute($value)
    {
        return $this->trashed() ? 'This message was deleted by '.(auth()->id() === $this->user_id ? 'you' : $this->user->name) : $value;
    }
}
