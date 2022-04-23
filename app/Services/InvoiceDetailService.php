<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Resources\InvoiceDetailResource;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ProductDetail;

class InvoiceDetailService
{
    private readonly InvoiceService $invoice_service;

    public function __construct(InvoiceService $invoice_service)
    {
        $this->invoice_service = $invoice_service;
    }

    public function update($id, array $data)
    {
        $invoiceDetail = InvoiceDetail::find($id);
        $productDetail = ProductDetail::find($invoiceDetail->product_detail_id);
        $product = $productDetail->product;
        $invoiceDetail->price = $data['price']??$productDetail->out_price;
        $invoiceDetail->quantity = $data['quantity'];
        // if ($data['status'] == Status::Accepted && $invoiceDetail->status == Status::Pending)
        // {
        //     $product->quantity -= $invoiceDetail->quantity;
        //     $invoiceDetail->remaining_quantity -= $invoiceDetail->quantity;
        // }
        // else if ($data['status'] == Status::AcceptedRefund
        // && ($invoiceDetail->status == Status::AcceptedRefund))
        // {
        //     $product->quantity += $invoiceDetail->quantity;
        //     $invoiceDetail->remaining_quantity += $invoiceDetail->quantity;
        // }
        $product->save();
        $productDetail->save();
        return $invoiceDetail->save();
    }

    public function delete($id)
    {
        $invoiceDetail = InvoiceDetail::find($id);
        $deleted = InvoiceDetail::destroy($id);
        if ($deleted > 0)
        {
            $this->invoice_service->refresh($invoiceDetail->invoice_id);
        }
        return $deleted;
    }

    public function create(array|InvoiceDetail $data)
    {
        $invoiceDetail = is_array($data) ?
            InvoiceDetail::create($data)
            : $data;
        if ($invoiceDetail->save()) {
            $invoice = Invoice::find($invoiceDetail->invoice_id);
            if ($invoice) {
                $query = InvoiceDetail::query()
                    ->where('invoice_id', '=', $invoice->id);
                $invoice->quantity = $query->sum('remaining_quantity');
                $invoice->option_count = $query->count();
                $invoice->save();
            }
            return $invoiceDetail->id;
        } else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = InvoiceDetail::query();
        if (isset($option['invoice_id'])) {
            $query->where('invoice_id', '=', $option['invoice_id']);
        }
        if ($option['with_detail'] == 'true') {
            $query->with('invoice');
            $query->with('productDetail');
        }
        // if ($option['search']) {
        //     $query->where('invoices.name', 'LIKE', "%".$option['search']."%");
        // }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        return InvoiceDetailResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = InvoiceDetail::query()
        ->where('id', $id)
        ->with('invoice')
        ->with('productDetail');
        return new InvoiceDetailResource($query->find($id));
    }
}
