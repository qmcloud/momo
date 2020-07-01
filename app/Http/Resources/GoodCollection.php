<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GoodCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
//        return [
//            'data' => $this->collection,
//            'links' => [
//                'self' => 'link-value',
//            ],
//        ];
//        return [
//            'id' => $this->id,
//            'goods_name' => $this->goods_name,
//        ];

    }
}
