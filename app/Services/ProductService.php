<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\ImageAssign;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductOption;
use Error;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function refresh($id)
    {
        /**
         * @var Product $product
         */
        $product = Product::find($id);
        if ($product) {
            $query = ProductDetail::query()
                ->where('product_id', '=', $product->id);
            $product->quantity = $query->sum('remaining_quantity');
            $product->option_count = $query->count();
            $product->min_price = $product->details->min('out_price');
            $product->max_price = $product->details->max('out_price');
            $product->save();
        }
    }

    public function update($id, array $data, array $options)
    {
        if (count($options) > ProductOption::where('product_id', $id)->count()) {
            throw new Error('Invalid to many options', 403);
        }
        $product = Product::find($id);
        if ($product != null) {
            if (!isset($product->default_image)) {
                $image = $product->images()->first();
                if ($image) {
                    $product->default_image = $image->blob()->get()->first()->id;
                }
            }
            $oldCateId = $product->category_id;
            /**
             * @var Product $product
             */
            $updated = $product
                ->update($data);
        } else {
            $updated = false;
        }
        if ($updated && $data['category_id'] != $oldCateId) {
            if ($oldCateId != null)
                Category::where('id', $oldCateId)
                    ->update(['product_count' => Product::where('category_id', $oldCateId)->count()]);
            if ($product->category_id != null)
                Category::where('id', $product->category_id)
                    ->update(['product_count' => Product::where('category_id', $product->category_id)->count()]);
        }
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
        /**
         * @var Product $product
         */
        $product = Product::find($id);
        ProductDetail::where('product_id', $id)->delete();
        $deleted = $product->delete();
        if ($deleted) {
            if ($product->category_id != null)
                Category::where('id', $product->category_id)
                    ->update(['product_count' => Product::where('category_id', $product->category_id)->count()]);
        }
        return $deleted;
    }

    public function create(array|Product $data)
    {
        $options = $data['options'];
        if ($options) {
            /**
             * @var Product $product
             */
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

                if ($product->category_id != null)
                    Category::where('id', $product->category_id)
                        ->update(['product_count' => Product::where('category_id', $product->category_id)->count()]);
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
        $user = Auth::user();
        if ($user == null || $user->is_admin == false) {
            $option['visible_only'] = 'true';
        }
        if ($option['consumableOnly'] == 'true') {
            $query->where('quantity', '>', '0');
        }
        if ($option['category_id'] != null) {
            $query->where('category_id', $option['category_id']);
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
        if (isset($option['hasImageOnly']) && $option['hasImageOnly'] == 'true') {
            $query->where('products.default_image', '!=', 'NULL');
        }
        if ($option['search']) {
            $query->leftJoin('categories', 'categories.id', '=', 'products.category_id');
            $query->where('products.name', 'LIKE', "%" . $option['search'] . "%", "or")
                ->orWhere('code', 'LIKE', "%" . $option['search'] . "%")
                ->orWhere('categories.name', 'LIKE', "%" . $option['search'] . "%");
        }
        if (isset($option['visible_only']) && $option['visible_only'] == 'true') {
            $query->where('products.visible', 1);
        }
        if ($option['max_price'] > 0)
        {
            $query->where('max_price' , '<=', $option['max_price']);
        }
        if ($option['min_price'] > 0) {
            $query->where('min_price' , '>=', $option['min_price']);
        }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        $query->orderBy('products.id', 'desc');
        return $query->paginate($page_size, page: $page_index);
    }

    public
    function getById(int $id)
    {
        $query = Product::query();
        $user = Auth::user();
        if ($user == null || $user->is_admin == false) {
            $query->where('visible', '1');
        }
        $query->with('details');
        $query->with('details.image');
        $query->with('images.blob');
        $query->with('image');
        $query->with('options');
        $query->with('category');
        $query->with('provider');
        $query->with('details.options');
        return new ProductResource($query->find($id));
    }
}
