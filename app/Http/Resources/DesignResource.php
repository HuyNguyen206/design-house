<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'title' => $this->title,
            'slug' => $this->slug,
            'is_live' => $this->is_live,
            'like' => [
              'count' => $this->whenLoaded('likedUsers')->count(),
              'like_user' =>  $this->whenLoaded('likedUsers')
            ],
            'description' => $this->description,
            'images' => $this->images,
            'tag_list' =>  [
                'tags' => $this->tagArray,
                'normalize' => $this->tagArrayNormalized
            ],
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'create_dates' => [
                'created_at_human' => $this->created_at->diffForHumans(),
                'created_at' => $this->created_at->format('d-m-Y h:i:s')
            ],
            'update_dates' => [
                'updated_at_human' => $this->updated_at->diffForHumans(),
                'updated_at' => $this->updated_at->format('d-m-Y h:i:s')
            ]
        ];
    }
}
