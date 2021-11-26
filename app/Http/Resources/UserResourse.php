<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResourse extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "created_at" => (string)$this->created_at,
            "updated_at" => (string)$this->updated_at,
            "favorite_animal" => $this->favorite_animal,
            "post" => postResourse::collection($this->posts()->get())
        ];
    }
}
