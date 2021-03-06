<?php

namespace App\Http\Resources;

use App\Models\Blob;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageAssignResource extends JsonResource
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
            'blob' => new BlobResource(Blob::find($this->blob_id)),
        ];
    }
}
