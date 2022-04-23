<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlobResource;
use App\Models\Blob;
use App\Models\ImageAssign;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class FileApiController extends Controller
{
    public function getListBlob(Request $request)
    {
        $query = Blob::query();
        if (isset($request['product_detail_id']) && $request['product_detail_id'] != 0) {
            $query->join('image_assigns', 'blobs.id', '=', 'image_assigns.blob_id')
                ->where('image_assigns.imageable_type', '=', 'App\\Models\\ProductDetail')
                ->where('image_assigns.imageable_id', '=', $request['product_detail_id'])
                ->get(['blobs.*']);
        } else if (isset($request['product_id']) && $request['product_id'] != 0) {
            $query->join('image_assigns', 'blobs.id', '=', 'image_assigns.blob_id')
                ->where('image_assigns.imageable_type', '=', 'App\\Models\\Product')
                ->where('image_assigns.imageable_id', '=', $request['product_id'])
                ->get(['blobs.*']);
        }
        $page_index = $request->get('page') ?? 1;
        $page_size = $request->get('limit') ?? 10;
        if ($request->get('search')) {
            $query->where('name', 'LIKE', '%' . $request->get('search') . '%', 'OR')
            ->where('file_path', 'LIKE', '%' . $request->get('search') . '%', 'OR');
        }
        if ($request->get('column') && $request->get('sort')) {
            $query->orderBy($request->get('column'), $request->get('sort'));
        }

        $result =  BlobResource::collection($query->paginate($page_size, page: $page_index));
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
            'data' => $result->items(),
            'meta' => [
                'total' => $result->total(),
                'perPage' => $result->perPage(),
                'currentPage' => $result->currentPage()
            ]
        ]);
    }

    public function duplicateBlob(Request $request, $id)
    {
        $blob = Blob::find($id);
        if ($blob) {
            $fileName = Uuid::fromDateTime(now()) . '.' . pathinfo($blob->file_path, PATHINFO_EXTENSION);
            if (Storage::copy($blob->file_path, $fileName)) {
                $blob = Blob::create([
                    'name' => $blob->name,
                    'file_path' => $fileName,
                    'created_by' => 20
                ]);
                return response()->json([
                    'code' => Response::HTTP_OK,
                    'status' => true,
                    'data' => new BlobResource($blob),
                ]);
            }
        }
        return response()->json([
            'code' => Response::HTTP_BAD_REQUEST,
            'status' => false,
        ]);
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $result = $file->store('');
        if ($result) {
            $blob = Blob::create([
                'name' => $request->get('name') ?? $file->getClientOriginalName(),
                'file_path' => $result,
                'created_by' => 20
            ]);
            return response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => new BlobResource($blob),
            ]);
        }
        return response()->json([
            'code' => Response::HTTP_BAD_REQUEST,
            'status' => false,
        ]);
    }

    public function updateBlob(Request $request, $id)
    {
        $file = $request->file('file');
        $blob = Blob::find($id);
        if ($blob) {

            $name = $request->get('name') ?? $blob->name;
            $name = pathinfo($name, PATHINFO_FILENAME);
            if ($file && $file->isValid()) {
                $iamgeSize = getimagesize($file);
                $name = preg_replace("/\(\d+x\d+\)/", '', $name);
                if ($iamgeSize) {
                    $name .= "({$iamgeSize[0]}x{$iamgeSize[1]})";
                }
                unlink(storage_path('app/' . $blob->file_path));
                $blob->file_path = $file->store('');
            }
            $blob->name = $name;
            $blob->save();
            return response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => $id,
            ]);
        } else {
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'status' => false,
            ]);
        }
    }

    public function uploadRange(Request $request)
    {
        $files = $request->file();
        $inserted = 0;
        foreach ($files as $file) {
            if ($file->isValid()) {
                $name = $request->get('name') ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $iamgeSize = getimagesize($file);
                if ($iamgeSize) {
                    $name .= "({$iamgeSize[0]}x{$iamgeSize[1]})";
                }
                $name .= '.' . $file->extension();
                $result = $file->store('');
                if ($result) {
                    Blob::create([
                        'name' => $name,
                        'file_path' => $result,
                        'created_by' => Auth::user()->id ?? 0
                    ]);
                    $inserted++;
                }
            }
        }
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => $inserted > 0,
            'data' => $inserted,
        ]);
    }

    // public function duplicateBlob(Request $request, int $id)
    // {
    //     $blob = Blob::firstOrFail
    // }

    public function get(Request $request, string $name)
    {
        $fileName = storage_path('app/' . $name);
        if (file_exists($fileName)) {
            return response()->file($fileName);
        }
    }
    public function download(Request $request, string $name)
    {
        $fileName = storage_path('app/' . $name);
        if (file_exists($fileName)) {
            return response()->download($fileName, $name);
        }
    }
    public function downloadById(Request $request, string $id)
    {
        $blob = Blob::find($id);
        if (isset($blob)) {
            $fileName = storage_path('app/' . $blob->file_path);
            if (file_exists($fileName)) {
                return response()->download($fileName, $blob->name);
            }
        }
    }
    public function getByBlob(Request $request, string $id)
    {
        $blob = Blob::find($id);
        if (isset($blob)) {
            $fileName = storage_path('app/' . $blob->file_path);
            if (file_exists($fileName)) {
                return response()->file($fileName);
            }
        }
    }
    public function delete(Request $request, $id)
    {
        $blob = Blob::find($id);
        if (isset($blob)) {
            $fileName = storage_path('app/' . $blob->file_path);
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            $deleted = $blob->delete();
            if ($deleted > 0) {
                ImageAssign::where('blob_id', $blob->id)->delete();
            }
            return response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => $blob->id,
            ]);
        } else {
            return response()->json([
                'code' => Response::HTTP_NOT_FOUND,
                'status' => false,
            ]);
        }
    }
    public function duplicatedFilter(Request $request)
    {
        $blobs = Blob::get();
        foreach ($blobs as $key => $value) {
            if ($value) {
                $file = Storage::get($value->file_path);
                if ($file) {
                    foreach ($blobs as $k => $v) {
                        if ($v->id != $value->id) {
                            $checkFile = Storage::get($v->file_path);
                            if ($checkFile == $file) {
                                $assigns = $v->assigns();
                                $assigns->update(['blob_id' => $value->id]);
                                unlink(storage_path('app/' . $v->file_path));
                                $v->delete();
                                unset($blobs[$k]);
                            }
                        }
                    }
                } else {
                    // $value::delete();
                }
            }
        }
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
        ]);
    }
}
