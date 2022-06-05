<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use Symfony\Component\HttpFoundation\Response;

class BlogApiController extends Controller
{
    private BlogService $blog_service;
    public function __construct(BlogService $blog_service)
    {
        $this->blog_service = $blog_service;
    }
    public function Index(Request $request)
    {
        try {
            $orderBy = [];
            if ($request->get('column') != null && $request->get('sort') != null) {
                $orderBy['sort'] = $request->get('sort');
                $orderBy['column'] = $request->get('column');
            }
            $blogPaginate = $this->blog_service
                ->getAll(
                    $orderBy,
                    $request->get('page') ?? 0,
                    $request->get('limit') ?? 10,
                    [
                        'search' => $request->get('search') ?? null,
                        'visible_only' => $request->get('visible_only') ?? false
                    ]
                );
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => $blogPaginate->items(),
                'meta' => [
                    'total' => $blogPaginate->total(),
                    'perPage' => $blogPaginate->perPage(),
                    'currentPage' => $blogPaginate->currentPage()
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
            $validator = Validator::make($data,  Blog::RULES);
            if ($validator->fails()) {
                $response = response()->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'status' => false,
                    'message' => $validator->errors()
                ]);
            } else {
                $data['created_by'] =  Auth::user()->id;
                $result = $this->blog_service->create($data);
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
            $result = $this->blog_service->getById($id);
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
            $rules = Blog::RULES;
            $rules['name'] .= ',name,' . $id;
            $validator = Validator::make($data,  $rules);
            if ($validator->fails()) {
                $response = response()->json([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'status' => false,
                    'meta' => $validator->errors(),
                    'message' => 'Failed'
                ]);
            } else {
                $data['last_updated_by'] =  Auth::user()->id;
                $result = $this->blog_service->update($id, $data);
                $response = response()->json([
                    'code' => Response::HTTP_OK,
                    'status' => $result,
                    'data' => $id,
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

    public function destroy(Request $request, $id)
    {
        try {
            $result = $this->blog_service->delete($id);
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
