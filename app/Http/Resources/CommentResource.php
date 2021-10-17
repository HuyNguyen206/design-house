<?php

namespace App\Http\Resources;

use App\Models\Design;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth()->user();
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'is_like_by_current_user' => $user ? $user->isLike($this->id, 'comment') : null,
            'user' => new UserResource($this->user),
            'commentable_type' => $this->commentable_type,
            'commentable' => $this->commentable
        ];
    }
}
