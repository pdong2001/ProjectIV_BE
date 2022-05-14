<?php

namespace App\Services;

use App\Http\Resources\ProviderResource;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;

class ProviderService
{
    public function update($id, array $data)
    {
        if (Auth::check())
        {
            $data['last_updated_by'] = Auth::user()->id;
        }
        $updated = Provider::where('id', $id)
        ->update($data);
        return $updated > 0;
    }

    public function delete($id)
    {
        return Provider::destroy($id);
    }

    public function create(array|Provider $data)
    {
        if (Auth::check())
        {
            $data['created_by'] = Auth::user()->id;
        }
        $provider = is_array($data) ?
            Provider::create($data)
            : $data;
        if($provider->save()) return $provider->id;
        else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Provider::query();
        if (isset($option['search']) && $option['search'] != '') {
            $query->where('name','LIKE', '%'.$option['search'].'%');
        }
        if (isset($option['visible_only']) && $option['visible_only'] == 'true')
        {
            $query->where('visible', true);
        }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        $query->orderBy('id', 'desc');
    return ProviderResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = Provider::query();
        return new ProviderResource($query->find($id));
    }
}
