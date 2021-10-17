<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $teamMembers = $this->members;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'members' => [
                'total_member' => $teamMembers->count(),
                'list_member' => UserResource::collection($teamMembers)
            ],
            'owner' => new UserResource($this->owner),
            'designs' => DesignResource::collection($this->designs)
        ];
    }
}
