<?php

namespace App\Services;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function refresh($id)
    {
        DB::statement("UPDATE customers SET debt = IFNULL((SELECT SUM(total - paid) FROM invoices WHERE customer_id = customers.id), 0) WHERE id = {$id}");
    }

    public function update($id, array $data)
    {
        if (Auth::check()) {
            $data['last_updated_by'] = Auth::user()->id;
        }
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
        if (Auth::check()) {
            $data['created_by'] = Auth::user()->id;
        }
        $customer = is_array($data) ?
            Customer::create($data)
            : $data;
        if ($customer->save()) return $customer->id;
        else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Customer::query();
        if (isset($option['search']) && $option['search'] != '') {
            $query->where('name', 'LIKE', '%' . $option['search'] . '%');
        }
        if (isset($option['visible_only']) && $option['visible_only'] == 'true') {
            $query->where('visible', true);
        }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        $query->orderBy('id', 'desc');
        return CustomerResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = Customer::query();
        return new CustomerResource($query->find($id));
    }
}
