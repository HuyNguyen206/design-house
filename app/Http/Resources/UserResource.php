<?php

namespace App\Http\Resources;

use App\Models\Design;
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
             $this->only(['id', 'user_name', 'name',
            'formatted_address', 'tag_line', 'about', 'location',
            'available_to_hire', 'avatar_url']),
            [
                'create_dates' => [
                    'created_at_human' => $this->created_at->diffForHumans(),
                    'created_at' => $this->created_at->format('d-m-Y h:i:s')
                ]
            ],
        [
            'designs' => DesignResource::collection($this->whenLoaded('designs')) ,
            $this->mergeWhen($this->id === auth()->id(), [
                'email' => $this->email
            ])
        ]);
    }
}
