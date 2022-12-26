<?php

namespace App\Services;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceDetail;

class InvoiceService
{
    public function __construct(CustomerService $customer_service)
    {
        $this->customer_service = $customer_service;
    }

    public function update($id, array $data)
    {
        $invoice = Invoice::find($id);
        if ($invoice->status == 4 || $invoice->status == 5)
        {
            return false;
        }
        unset($data['status_name']);
        unset($data['created_at']);
        unset($data['updated_at']);
        if ($invoice->customer_id != $data['customer_id']) {
            $customer = $invoice->customer;
            $data['customer_name'] = $customer->name;
            $data['phone_number'] = $customer->phone_number;
            $data['address'] = $customer->address;
            $data['district'] = $customer->district;
            $data['commune'] = $customer->commune;
            $data['province'] = $customer->province;
        }
        $updated = Invoice::where('id', $id)
            ->update($data);
        if ($invoice->customer_id != $data['customer_id']) {
            $this->customer_service->refresh($data['customer_id']);
            $this->customer_service->refresh($invoice->customer_id);
        }
        return $updated > 0;
    }

    public function delete($id)
    {
        $invoice = Invoice::find($id);
        if ($invoice) {
            $deleted = Invoice::destroy($id);
            $this->customer_service->refresh($invoice->customer_id);
        }
        else{
            return 0;
        }
        return $deleted;
    }

    public function create(array|Invoice $data)
    {
        $invoice = is_array($data) ?
            Invoice::create($data)
            : $data;
        if ($invoice->save()) {
            $this->customer_service->refresh($invoice->customer_id);
            return $invoice->id;
        } else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Invoice::query();
        if ($option['with_detail'] == 'true') {
            $query->with('details.productDetail');
            $query->with('customer');
        }
        if (isset($option['customer']) && $option['customer'] != null) {
            $query->where('customer_id', $option['customer']);
        }
        if (isset($option['status']) && $option['status'] != null) {
            $query->where('status', $option['status']);
        }
        // if ($option['search']) {
        //     $query->where('name', 'LIKE', "%".$option['search']."%")
        //     ->orWhere('code', 'LIKE', "%".$option['search']."%");
        // }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        return InvoiceResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = Invoice::query();
        $query->with('details.productDetail');
        $query->with('customer');
        return new InvoiceResource($query->find($id));
    }
}
