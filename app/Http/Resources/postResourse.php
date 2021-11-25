<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class postResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            "id"=>$this->id,
            "body"=>$this->body,
            "visibility"=>$this->visibility,
            "created_at"=>(string)$this->created_at,
            "created_at"=>(string)$this->created_at,
            "post"=>$this->comments()->get()
        ];
    }
}
