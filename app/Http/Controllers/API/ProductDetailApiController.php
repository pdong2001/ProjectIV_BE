<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Models\ProductDetail;
use App\Services\ProductDetailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductDetailApiController extends Controller
{
    private ProductDetailService $product_detail_service;
    public function __construct(ProductDetailService $product_detail_service)
    {
        $this->product_detail_service = $product_detail_service;
    }
    public function Index(Request $request)
    {
        try {
            $orderBy = [];
            if ($request->get('column') && $request->get('sort')) {
                $orderBy['sort'] = $request->get('sort');
                $orderBy['column'] = $request->get('column');
            }
            $productPaginate = $this->product_detail_service
                ->getAll(
                    $orderBy,
                    $request->get('page') ?? 0,
                    $request->get('limit') ?? 10,
                    [
                        'consumableOnly' => $request->get('consumable_only') ?? false,
                        'with_detail' => $request->get('with_detail') ?? false,
                        'product_id' => $request->get('product_id'),
                        'search' => $request->get('search'),
                        'with_product' => $request->get('with_product')
                    ]
                );
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => $productPaginate->items(),
                'meta' => [
                    'total' => $productPaginate->total(),
                    'perPage' => $productPaginate->perPage(),
                    'currentPage' => $productPaginate->currentPage(),
                    // 'lastPage' => $productPaginate->lastPage()
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
            $data['created_by']=20;
            $validator = Validator::make($data,  ProductDetail::RULES);
            if ($validator->fails()) {
                $response = response()->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'status' => false,
                    'message' => $validator->errors()
                ]);
            } else {
                $data['created_by'] = 20;
                    $result = $this->product_detail_service->create($data);
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
            $result = $this->product_detail_service->getById($id);
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
        try {
            $data = $request->all();
            $data['last_updated_by'] = 20;
            $result = $this->product_detail_service->update($id, $data);
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => $result,
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

    public function destroy(Request $request, $id)
    {
        try {
            $result = $this->product_detail_service->delete($id);
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
