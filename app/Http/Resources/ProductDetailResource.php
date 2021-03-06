<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product = $this->whenLoaded('product');
        if (!isset($this->name) || $this->name == null) $this->name = Product::find($this->product_id)->name;
        return [
            'id' => $this->id,
            'product' => new ProductResource($product),
            'name' => $this->name,
            'product_id' => $product?null:$this->product_id,
            'option_name' => $this->option_name,
            'option_value' => $this->option_value,
            'out_price' => $this->out_price,
            'in_price' => $this->whenLoaded('in_price')?$this->in_price:null,
            'remaining_quantity' => $this->remaining_quantity,
            'total_quantity' => $this->total_quantity,
            'unit' => $this->unit,
            'visible' => $this->visible,
            'default_image' => $this->default_image,
            'images' => ImageAssignResource::collection($this->whenLoaded('images')),
            'image' => new BlobResource($this->whenLoaded('image')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'options' => ProductDetailOptionValueResource::collection($this->whenLoaded('options'))
        ];
    }
}
