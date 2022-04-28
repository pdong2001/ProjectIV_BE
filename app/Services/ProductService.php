<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Models\ImageAssign;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductOption;
use Error;

class ProductService
{
    public function refresh($id)
    {
        $product = Product::find($id);
        if ($product) {
            $query = ProductDetail::query()
                ->where('product_id', '=', $product->id);
            $product->quantity = $query->sum('remaining_quantity');
            $product->option_count = $query->count();
            $product->save();
        }
    }

    public function update($id, array $data, array $options)
    {
        if (count($options) > ProductOption::where('product_id', $id)->count())
        {
            throw new Error('Invalid to many options', 403);
        }
        $updated = Product::where('id', $id)
            ->update($data);
        if (isset($options)) {
            foreach ($options as $key => $value) {
                if (isset($value['id']) && isset($value['name'])) {
                    $optObj = ProductOption::find($value['id']);
                    if ($optObj != null) {
                        $optObj->name = $value['name'];
                        $optObj->save();
                    }
                }
            }
        }
        return $updated > 0;
    }

    public function delete($id)
    {
        ImageAssign::where('imageable_id', $id)
            ->where('imageable_type', 'App\\Models\\Product')
            ->delete();
        ProductDetail::where('product_id', $id)->delete();
        $deleted = Product::destroy($id);
        return $deleted;
    }

    public function create(array|Product $data)
    {
        $options = $data['options'];
        if ($options) {
            $product = is_array($data) ?
                Product::create($data)
                : $data;
            if ($product->save()) {
                foreach ($options as $key => $value) {
                    if (!isset($value['name']) || $value['name'] == '') {
                        $product->options()->delete();
                        $product->delete();
                        throw new Error("Invalid options", 403);
                    }
                    ProductOption::create([
                        'name' => $value['name'],
                        'product_id' => $product->id
                    ]);
                }
                return $product->id;
            }
        }
        return 0;
    }

    public
    function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = Product::query();
        if ($option['consumableOnly'] == 'true') {
            $query->where('quantity', '>', '0');
        }
        if ($option['with_images'] == 'true') {
            $query->with('images.blob');
        }
        if ($option['with_detail'] == 'true') {
            if ($option['consumableOnly'] == 'true') {
                $query->with(['details' => function ($q) {
                    $q->where('remaining_quantity', '>', '0');
                }]);
            } else {
                $query->with('details');
            }
            $query->with('details.image');
        }
        $query->with('image');
        $query->with('category');
        $query->with('options');
        if ($option['search']) {
            $query->where('name', 'LIKE', "%" . $option['search'] . "%")
                ->orWhere('code', 'LIKE', "%" . $option['search'] . "%");
        }
        if (isset($option['visible_only'])) {
            $query->where('visible', $option['visible_only'] == "false" ? 0 : 1);
        }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        $query->orderBy('id', 'desc');
        return $query->paginate($page_size, page: $page_index);
    }

    public
    function getById(int $id)
    {
        $query = Product::query();
        $query->with('details.image');
        $query->with('images.blob');
        $query->with('image');
        $query->with('options');
        return new ProductResource($query->find($id));
    }
}
