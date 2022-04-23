<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $category = $this->whenLoaded(relationship:'category');
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'name' => $this->name,
            'category' => new CategoryResource($category),
            'code' => $this->code,
            'default_image' => $this->default_image,
            'category_id' => $this->category_id,
            'option_count' => $this->option_count,
            'quantity' => $this->quantity,
            'images' => ImageAssignResource::collection($this->whenLoaded('images')),
            'default_image' => new BlobResource($this->whenLoaded('image')),
            'details' => ProductDetailResource::collection($this->whenLoaded('details')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'provider' => new CategoryResource($this->whenLoaded('provider')),
            'visible' => $this->visible,
            'description' => $this->description,
            ...Product::tArray($this)
        ];
    }
}
