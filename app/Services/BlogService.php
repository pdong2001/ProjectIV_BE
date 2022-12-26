<?php

namespace App\Services;

use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class BlogService
{
    public function update($id, array $data)
    {
        $updated = Blog::where('id', $id)
        ->update($data);
        return $updated > 0;
    }

    public function delete($id)
    {
        return Blog::destroy($id);
    }

    public function create(array|Blog $data)
    {
        $blog = is_array($data) ?
            Blog::create($data)
            : $data;
        if($blog->save()) return $blog->id;
        else return 0;
    }

    public function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Blog::query();
        $query->with('image');
        if (isset($option['search']) && $option['search'] != '') {
            $option['search'] = str_replace([" ",'_','-'], '.*', trim($option['search']));
            $query->where(function ($query) use ($option) {
                $query->orwhere('title','RLIKE', $option['search']);
                $query->orwhere('short_description','RLIKE', $option['search']);
                $query->orwhere('content','RLIKE', $option['search']);
            });
        }
        if (isset($option['visible_only']) && $option['visible_only'] == 'true')
        {
            $query->where('visible', true);
        }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        $query->orderBy('id', 'desc');
    return BlogResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = Blog::query();
        $query->with('image');
        return new BlogResource($query->find($id));
    }
}
