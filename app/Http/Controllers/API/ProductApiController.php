<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductApiController extends Controller
{
    private ProductService $product_service;
    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
    }
    public function Index(Request $request)
    {
        try {
            $orderBy = [];
            if ($request->get('column') && $request->get('sort')) {
                $orderBy['sort'] = $request->get('sort');
                $orderBy['column'] = $request->get('column');
            }
            $data = $this->product_service
                ->getAll(
                    $orderBy,
                    $request->get('page') ?? 0,
                    $request->get('limit') ?? 10,
                    [
                        'consumableOnly' => $request->get('consumable_only') ?? false,
                        'search' => $request->get('search') ?? null,
                        'with_detail' => $request->get('with_detail') ?? false,
                        'with_images' => $request->get('with_images') ?? false,
                    ]
                );
            $productPaginate = ProductResource::collection($data);
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => $productPaginate->items(),
                'meta' => [
                    'total' => $productPaginate->total(),
                    'perPage' => $productPaginate->perPage(),
                    'currentPage' => $productPaginate->currentPage()
                ]
            ]);
        } catch (\Throwable $th) {
            $response = response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }

        return $response;
    }

    public function store(Request $request)
    {
        try {
            $data = $request->post();
            $validator = Validator::make($data,  [
                ...Product::RULES,
                'options' => 'required|min:1'
            ]);
            if ($validator->fails()) {
                $response = response()->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'status' => false,
                    'message' => $validator->errors()
                ]);
            } else {
                $data['created_by'] =  Auth::user()->id;
                $result = $this->product_service->create($data, $data['options']);
                $response = response()->json([
                    'code' => Response::HTTP_OK,
                    'status' => $result > 0,
                    'data' => $result,
                    'meta' => []
                ]);
            }
        } catch (\Throwable $th) {
            $response = response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
        return $response;
    }

    public function show(Request $request, $id)
    {
        try {
            $result = $this->product_service->getById($id);
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => $result,
                'meta' => []
            ]);
        } catch (\Throwable $th) {
            $response = response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
        return $response;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, Product::RULES);
        $data = $request->except('options');
        $data['last_updated_by'] =  Auth::user()->id;
        $result = $this->product_service->update($id, $data, $request->options);
        $response = response()->json([
            'code' => Response::HTTP_OK,
            'status' => $result,
            'data' => $id,
            'meta' => []
        ]);

        return $response;
    }

    public function destroy(Request $request, $id)
    {
        try {
            $result = $this->product_service->delete($id);
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => $result > 0,
                'data' => $id,
                'meta' => []
            ]);
        } catch (\Throwable $th) {
            $response = response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
        return $response;
    }
}
