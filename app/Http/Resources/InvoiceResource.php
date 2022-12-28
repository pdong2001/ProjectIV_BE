<?php

namespace App\Http\Resources;

use App\Models\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'address' => $this->address,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'total' => $this->total,
            'paid' => $this->paid,
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer_name,
            'phone_number' => $this->phone_number,
            'district' => $this->district,
            'option_count' => $this->option_count,
            'commune' => $this->commune,
            'province' => $this->province,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'details' => InvoiceDetailResource::collection($this->whenLoaded('details')),
            'status' => $this->status,
            'status_name' => Invoice::getStatusName($this->status),
            'cancel_pending' => $this->cancel_pending
        ];
    }
}
