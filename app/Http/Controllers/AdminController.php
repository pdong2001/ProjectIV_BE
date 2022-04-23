<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class AdminController extends Controller
{
    public ProductService $productService;
    function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    function Index(Request $request)
    {
        return view('admin/pages/home');
    }
    function  Category(Request $request)
    {
        return view('admin/pages/category');
    }
    function  Product(Request $request)
    {
        return view('admin/pages/product');
    }
    function  ProductDetail(Request $request, int $id)
    {
        $product = $this->productService->getById($id);
        if ($product)
        {
            return view('admin/pages/product-detail', ['product' => $product]);
        }
        else
        {
            throw new NotFoundResourceException();
        }
    }
    function  ProductDetails(Request $request)
    {
        return view('admin/pages/product-detail-list');
    }
}
