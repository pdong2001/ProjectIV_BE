<?php

namespace App\Services;

use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\ImageAssign;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductDetailOptionValue;
use Error;

class ProductDetailService
{
    private readonly ProductService $product_service;
    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
    }

    public function update($id, array $data, array $options)
    {
        /**
         * @var ProductDetail $productDetail
         */
        $productDetail = ProductDetail::find($id);
        $updated = $productDetail->update($data);
        if ($updated && isset($options)) {
            foreach ($options as $key => $value) {
                // if (isset($value['value']) && isset($value['option_id'])) {
                $optionValue = ProductDetailOptionValue::where('product_detail_id', $id)
                    ->where('option_id', $value['option_id'])->firstOrFail();
                if ($optionValue) {
                    $optionValue->value = $value['value'];
                    $optionValue->save();
                }
                // }
            }
            $this->product_service->refresh($productDetail->product_id);

            if ($productDetail->product_id != $data['product_id']) {
                $this->product_service->refresh($data['product_id']);
            }
        }
        if ($updated && ($productDetail->product_id != $data['product_id'] ||
            $productDetail->remaining_quantity != $data['remaining_quantity'])) {
        }
        return $updated > 0;
    }

    public function delete($id)
    {
        $productDetail = ProductDetail::find($id);
        $deleted = ProductDetail::destroy($id);
        if ($productDetail != null && $deleted > 0) {
            $this->product_service->refresh($productDetail->product_id);
            ImageAssign::where('imageable_id', $id)
                ->where('imageable_type', 'App\\Models\\ProductDetail')
                ->delete();
        }
        return $deleted;
    }

    public function create(array |ProductDetail $data, array $options)
    {
        $productDetail = is_array($data) ?
            new ProductDetail($data)
            : $data;
        if (!isset($options) || count($options) != Product::find($data['product_id'])->options()->count()) {
            throw new Error('Invalid options', 403);
        }
        if ($productDetail->save()) {
            foreach ($options as $key => $value) {
                if (isset($value['value']) && isset($value['option_id']))
                    ProductDetailOptionValue::create([
                        'value' => $value['value'],
                        'option_id' => $value['option_id'],
                        'product_detail_id' => $productDetail->id
                    ]);
                else {
                    $productDetail->delete();
                    throw new Error('Invalid options', '403');
                }
            }
            $product = Product::find($productDetail->product_id);
            if ($product) {
                $query = ProductDetail::query()
                    ->where('product_id', '=', $product->id);
                $product->quantity = $query->sum('remaining_quantity');
                $product->option_count = $query->count();
                $product->save();
            }

            return $productDetail->id;
        } else {
            return 0;
        }
    }

    public
    function getAll(
        array $orderBy = [],
        int $page_index = 0,
        int $page_size = 10,
        array $option = []
    ) {
        $query = ProductDetail::query();
        $query->with('options');
        if ($option['consumableOnly'] == 'true') {
            $query->where('remaining_quantity', '>', '0');
        }
        $query->with('image');

        if (isset($option['with_product'])) {
            $query->with('product');
        }

        if (isset($option['product_id'])) {
            $query->where('product_id', '=', $option['product_id']);
        }
        if ($option['with_detail'] == 'true') {
            $query->with('images.blob');
            $query->with('product.image');
        }
        if (isset($option['search']) && $option['search'] != '') {
            $query->join('products', 'product_details.product_id', '=', 'products.id')
                ->where('products.name', 'LIKE', "%" . $option['search'] . "%", 'OR')
                ->where('product_details.option_value', 'LIKE', "%" . $option['search'] . "%", 'OR')
                ->where('product_details.option_name', '=', $option['search'], 'OR')
                ->where('product_details.unit', '=', $option['search'], 'OR')
                ->select(['product_details.*', 'products.name']);
        }
        if ($orderBy) {
            $query->orderBy($orderBy['column'], $orderBy['sort']);
        }
        return ProductDetailResource::collection($query->paginate($page_size, page: $page_index));
    }

    public function getById(int $id)
    {
        $query = ProductDetail::query();
        $query->with('product');
        $query->with('images.blob');
        $query->with('image');
        $query->with('options');
        return new ProductDetailResource($query->find($id));
    }
}
