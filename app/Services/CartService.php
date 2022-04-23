<?php

namespace App\Services;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartService
{
    public function checkOut(array $data)
    {
        $validator = Validator::make($data,Invoice::RULES);
        if (!$validator->failed())
        {
            if (!isset($data['customer_id']))
            {
                $customer = Customer::create([
                    'name' => $data['customer_name'],
                    'address' => $data['address'],
                    'phone_number' => $data['phone_number'],
                ]);
            }
            if ($customer->save())
            {
                $data['customer_id'] = $customer->id;
            }
            $invoice = Invoice::create($data);
            if ($invoice->save())
            {
                $cart_products = Cart::find($data['carts']);
                $details = [];
                foreach ($cart_products as $value) {
                    array_push($details, InvoiceDetail::create([
                        'product_detail_id' => $value->product_detail_id,
                        'quantity' => $value->quantity,
                        'price' => $value->productDetail->out_price,
                        'invoice_id' => $invoice->id 
                    ]));
                }
                if (DB::table('invoice_details')->insert($details))
                {
                    Cart::destroy($cart_products);
                    $invoice->total = InvoiceDetail::query()
                    ->where('invoice_id',$invoice->id)
                    ->sum('quantity*price');
                    $invoice->save();
                }
            }
            
        }
    }

    public function update($id, array $data)
    {
        $updated = Cart::where('id', $id)
        ->update($data);
        return $updated > 0;
    }

    public function delete($id)
    {
        return Cart::destroy($id);
    }

    public function create(array|Cart $data)
    {
        $cart = is_array($data) ?
            Cart::create($data)
            : $data;
        if($cart->save()) return $cart->id;
        else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Cart::query();
        if ($option['with_detail'] == 'true') {
            $query->with('productDetail.product');
        }
        if (isset($option['customer_id']) && $option['customer_id'] != null)
        {
            $query->where('customer_id', $option['customer_id']);
        }
        // if ($option['search']) {
        //     $query->where('name', 'LIKE', "%".$option['search']."%")
        //     ->orWhere('code', 'LIKE', "%".$option['search']."%");
        // }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        return CartResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = Cart::query();
        $query->with('productDetail.product');
        return new CartResource($query->find($id));
    }
}
