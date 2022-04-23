<?php

namespace App\Services;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Invoice;

class CustomerService
{
    public function refresh($id)
    {
        $customer = Customer::find($id);
        if ($customer)
        {
            $customer->dept = Invoice::query()
            ->where('customer_id',$customer->id)
            ->where('total', '>', 'paid')
            ->sum('total-paid');
            $customer->save();
        }
    }

    public function update($id, array $data)
    {
        $updated = Customer::where('id', $id)
        ->update($data);
        return $updated > 0;
    }

    public function delete($id)
    {
        return Customer::destroy($id);
    }

    public function create(array|Customer $data)
    {
        $customer = is_array($data) ?
            Customer::create($data)
            : $data;
        if($customer->save()) return $customer->id;
        else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Customer::query();
        if ($option['with_detail'] == 'true') {
            $query->with('details.productDetail');
            $query->with('customer');
        }
        // if ($option['search']) {
        //     $query->where('name', 'LIKE', "%".$option['search']."%")
        //     ->orWhere('code', 'LIKE', "%".$option['search']."%");
        // }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        return CustomerResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = Customer::query();
        $query->with('details.productDetail');
        $query->with('customer');
        return new CustomerResource($query->find($id));
    }
}
