<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Models\ImageAssign;
use App\Models\Product;
use App\Models\ProductDetail;

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

    public function update($id, array $data)
    {
        $updated = Product::where('id', $id)
            ->update($data);
        return $updated > 0;
    }

    public function delete($id)
    {
        $product = Product::find($id);
        $deleted = Product::destroy($id);
        if ($product != null && $deleted > 0) {
            ImageAssign::where('imageable_id', $id)
                ->where('imageable_type', 'App\\Models\\Product')
                ->delete();
        }
        return $deleted;
    }

    public function create(array|Product $data)
    {
        $product = is_array($data) ?
            Product::create($data)
            : $data;
        if($product->save()) return $product->id;
        else return 0;
    }

public
function getAll(
    array $orderBy = [],
    int $page_index = 0,
    int $page_size = 10,
    array $option = []
)
{
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
    return ProductResource::collection($query->paginate($page_size, page: $page_index));
    }

public
function getById(int $id)
{
    $query = Product::query();
    $query->with('details.image');
    $query->with('images.blob');
    $query->with('image');

    return new ProductResource($query->find($id));
}
}
