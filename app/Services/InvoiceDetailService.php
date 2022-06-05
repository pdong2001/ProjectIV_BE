<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Resources\InvoiceDetailResource;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ProductDetail;
use Error;
use Illuminate\Support\Facades\DB;

class InvoiceDetailService
{

    public function __construct(InvoiceService $invoice_service, CustomerService $customer_service)
    {
        $this->invoice_service = $invoice_service;
        $this->customer_service = $customer_service;
    }

    public function delete($id)
    {
        $invoiceDetail = InvoiceDetail::find($id);
        $invoice = Invoice::find($invoiceDetail->invoice_id);
        if ($invoice == null || $invoice->status > 3) {
            throw new Error("Invalid request", 403);
        }
        $productDetail = $invoiceDetail->productDetail;
        $deleted = InvoiceDetail::destroy($id);
        if ($deleted > 0) {
            DB::statement("UPDATE invoices SET total = IFNULL((SELECT SUM(quantity * price) FROM invoice_details WHERE invoice_id = invoices.id),0), option_count = IFNULL((SELECT COUNT(*) FROM invoice_details WHERE invoice_id = invoices.id),0) WHERE id = {$invoice->id}");
            $this->customer_service->refresh($invoice->customer_id);
            DB::statement("UPDATE product_details SET remaining_quantity = remaining_quantity + {$invoiceDetail->quantity} WHERE id = {$invoiceDetail->product_detail_id}");
            if ($productDetail != null) {

                DB::statement("UPDATE products SET quantity = IFNULL((SELECT SUM(remaining_quantity) FROM product_details WHERE product_id = products.id),0) WHERE id = {$productDetail->product_id}");
            }
        }
        return $deleted;
    }

    public function create(array $data)
    {
        $invoice = Invoice::find($data['invoice_id']);
        if ($invoice == null || $invoice->status > 3) {
            throw new Error("Invalid request", 403);
        }
        $productDetail = ProductDetail::find($data['product_detail_id']);
        if (!isset($data['price']) || $data['price'] == null) {
            $data['price'] = $productDetail->out_price;
        }
        $invoiceDetail = InvoiceDetail::where('invoice_id', $invoice->id)->where('product_detail_id', $data['product_detail_id'])
            ->where('price', $data['price'])->first();
        if ($invoiceDetail != null) {
            $invoiceDetail->quantity += $data['quantity'];
        } else {

            $invoiceDetail = new InvoiceDetail($data);
        }

        if ($invoiceDetail->save()) {
            if ($invoice != null) {
                DB::statement("UPDATE invoices SET total = IFNULL((SELECT SUM(quantity * price) FROM invoice_details WHERE invoice_id = invoices.id),0), option_count = IFNULL((SELECT COUNT(*) FROM invoice_details WHERE invoice_id = invoices.id),0) WHERE id = {$invoice->id}");
                $this->customer_service->refresh($invoice->customer_id);
                DB::statement("UPDATE product_details SET remaining_quantity = remaining_quantity + {$invoiceDetail->quantity} WHERE id = {$invoiceDetail->product_detail_id}");

                if ($productDetail != null) {

                    DB::statement("UPDATE products SET quantity = IFNULL((SELECT SUM(remaining_quantity) FROM product_details WHERE product_id = products.id),0) WHERE id = {$productDetail->product_id}");
                }
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
        if (isset($option['invoice_id']) && $option['invoice_id'] != null) {
            $query->where('invoice_id', '=', $option['invoice_id']);
        }
        if (isset($option['with_invoice']) && $option['with_invoice'] == 'true')
            $query->with('invoice');
        if ($option['with_detail'] == 'true') {
            $query->with('productDetail.options');
        }
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
            ->with('productDetail.image')
            ->with('productDetail.product.image');
        return new InvoiceDetailResource($query->find($id));
    }
}
