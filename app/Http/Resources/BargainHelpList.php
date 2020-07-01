<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\User;

class BargainHelpList extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::find($this->uid);
        return [
            "id" => $this->id,
            "uid" => $this->uid,
            "name" => $user->name,
            "avatar" => $user->avatar,
            "price" => $this->price,
            "created_time" => $this->created_at,
        ];
    }

}
