<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $invoice = $this->whenLoaded('invoice');
        $productDetail = $this->whenLoaded('productDetail');
        return [
            'id' => $this->id,
            'product_detail_id' => $this->product_detail_id,
            'invoice_id' => $this->invoice_id,
            'product_detail' => new ProductDetailResource($productDetail),
            'invoice' => new InvoiceResource($invoice),
            'price' => $this->price,
            'quantity' => $this->quantity,
            'total' => $this->quantity * $this->price,
        ];
    }
}
