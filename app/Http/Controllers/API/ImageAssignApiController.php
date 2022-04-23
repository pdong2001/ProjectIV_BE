<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageAssignResource;
use App\Models\Blob;
use App\Models\ImageAssign;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ImageAssignApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'blob_id' => 'required_if:file,null',
                'file' => 'required_if:blob_id,null',
                'imageable_id' => 'required',
                'imageable_type' => [
                    'required',
                    Rule::in(['App\Models\Product', 'App\Models\ProductDetail'])
                ]
            ]);
            $data = $request->post();
            $file = $request->file('file');
            if ($file != null) {
                $name = $data['name'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $iamgeSize = getimagesize($file);
                if ($iamgeSize) {
                    $name .= "({$iamgeSize[0]}x{$iamgeSize[1]})";
                }
                $name .= '.' . $file->extension();
                $path = $file->store('');
                $blob = Blob::create([
                    'name' => $name,
                    'file_path' => $path,
                    'created_by' => Auth::user()->id
                ]);
                $data['blob_id'] = $blob->id;
            }
            $data['created_by'] = Auth::user()->id;
            $result = ImageAssign::create($data);
            $response = response()->json([
                'code' => Response::HTTP_OK,
                'status' => true,
                'data' => new ImageAssignResource($result),
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
            $result = ImageAssign::destroy($id);
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
