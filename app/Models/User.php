<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tag_line',
        'about',
        'user_name',
        'location',
        'available_to_hire',
        'formatted_address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $spatialFields = [
        'location',
    ];

    public function sendEmailVerificationNotification()
    {
       $this->notify(new \App\Notifications\VerifyEmail());
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }


    public function likedComments()
    {
        return $this->morphedByMany(Comment::class, 'likeable')->withTimestamps();
    }


    public function likedDesigns()
    {
        return $this->morphedByMany(Design::class, 'likeable')->withTimestamps();
    }

    public function likeToggle(int $id, string $type)
    {
        $this->{"liked$type"}()->toggle($id);
    }

    public function isLike($id, $type)
    {
        $typeTable = Str::of($type)->lower()->plural();
        $type = Str::of($typeTable)->ucfirst();
        if (!DB::table($typeTable)->where('id', $id)->exists()) {
            throw new ModelNotFoundException("Model $type $id not exist");
        }
       return $this->{"liked$type"}()->where("$typeTable.id", $id)->exists();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    public function ownTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function isOwnerOfTeam($teamId)
    {
        return $this->ownTeams()->where('id', $teamId)->exists();
    }

    public function sendInviteTeams()
    {
        return $this->belongsToMany(Team::class, 'invitations', 'sender_id');
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'participants')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getChatWithUser($userId)
    {
        $user = auth()->user();
        return $user->chats()->whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();
    }


}
