<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CartApiController extends Controller
{
    private CartService $cart_service;
    public function __construct(CartService $cart_service)
    {
        $this->cart_service = $cart_service;
    }

    public function checkOut(Request $request)
    {
        if (Auth::check()) {
            /**
             * @var App/Model/User $user
             */
            $user = Auth::user();
            $deleted = Cart::query()->where('customer_id',  $user->customer->id)->delete();

            return response()->json([
                'status' => true,
                'data' => $deleted,
                'code' => 200
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => 401
            ]);
        }
    }

    public function Index(Request $request)
    {
        try {
            $orderBy = [];
            if ($request->get('column') && $request->get('sort')) {
                $orderBy['sort'] = $request->get('sort');
                $orderBy['column'] = $request->get('column');
            }
            $cartPaginate = $this->cart_service
                ->getAll(
                    $orderBy,
                    $request->get('page') ?? 0,
                    $request->get('limit') ?? 10,
                    [
                        'search' => $request->get('search') ?? null,
                        'with_detail' => $request->get('with_detail') ?? false,
                        'customer_id' => Auth::user()->customer->id
                    ]
                );
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => $cartPaginate->items(),
                'meta' => [
                    'total' => $cartPaginate->total(),
                    'perPage' => $cartPaginate->perPage(),
                    'currentPage' => $cartPaginate->currentPage()
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

    public function storeRange(Request $request)
    {
        $this->validate($request, [
            'data' => 'required|array',
            'data.*' => 'required|distinct',
            'data.*.quantity' => 'required'
        ]);
        $data = $request->data;
        $customer_id = Auth::user()->customer->id;
        if ($data && $customer_id)
            foreach ($data as $key => $value) {
                $cart = Cart::where('customer_id', $customer_id)->where('product_detail_id', $value->product_detail_id)->firstOrFault();
                if ($cart) {
                    $cart->quantity += $request->quanity;
                    $cart->save();
                } else {
                    $this->cart_service->create($data);
                }
            }
        return response()->json([
            'status' => true,
            'code' => Response::HTTP_OK,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->post();
            $validator = Validator::make($data,  Cart::RULES);
            if ($validator->fails()) {
                $response = response()->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'status' => false,
                    'message' => $validator->errors()
                ]);
            } else {
                $user = Auth::user();
                $customer_id = $user->customer->id;
                $data['customer_id'] = $customer_id;

                $result = $this->cart_service->create($data);
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
            $result = $this->cart_service->getById($id);
            if ($result->resource != null) {
                $response = response()->json([
                    'code' => Response::HTTP_OK,
                    'status' => true,
                    'data' => $result,
                    'meta' => []
                ]);
            } else {
                $response = response()->json([
                    'code' => Response::HTTP_NOT_FOUND,
                    'status' => false,
                    'message' => Response::$statusTexts[Response::HTTP_NOT_FOUND],
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

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $result = $this->cart_service->update($id, $data);
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
            $result = $this->cart_service->delete($id);
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
