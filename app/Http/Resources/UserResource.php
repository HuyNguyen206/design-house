<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(
            [
                'create_dates' => [
                    'created_at_human' => $this->created_at->diffForHumans(),
                    'created_at' => $this->created_at->format('d-m-Y h:i:s')
                ]
            ], $this->only(['id', 'username', 'email', 'name',
            'formatted_address', 'tag_line', 'about', 'location',
            'available_to_hire']));
    }
}
