<?php

namespace App\Http\Resources;

use App\Models\ProductOption;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailOptionValueResource extends JsonResource
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
            'value' => $this->value,
            'option_id' => $this->option_id,
            'name' => ProductOption::find($this->option_id)->name
        ];
    }
}
