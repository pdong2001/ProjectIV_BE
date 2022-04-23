<?php

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function update($id, array $data)
    {
        if (Auth::check())
        {
            $data['updated_by'] = Auth::user()->id;
        }
        $updated = Category::where('id', $id)
        ->update($data);
        return $updated > 0;
    }

    public function delete($id)
    {
        return Category::destroy($id);
    }

    public function create(array|Category $data)
    {
        if (Auth::check())
        {
            $data['created_by'] = Auth::user()->id;
        }
        $category = is_array($data) ?
            Category::create($data)
            : $data;
        if($category->save()) return $category->id;
        else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Category::query();
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
    return CategoryResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = Category::query();
        return new CategoryResource($query->find($id));
    }
}
