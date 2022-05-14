<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebInfoResource;
use App\Models\Blob;
use App\Models\WebInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class WebInforApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = WebInfo::query();
        if ($request->get('name') != null) {
            $query->where('name', $request->get('name'));
        }
        $query->with('image');
        $webInfos = $query->get();
        if ($webInfos != null) {
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'data' => WebInfoResource::collection($webInfos),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_NOT_FOUND,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, WebInfo::RULES);
        $info = new WebInfo($request->all());
        $file = $request->file('file');
        if ($file != null && $file->isValid()) {
            $file_path = $file->store('');
            $blob = Blob::create([
                'file_path' => $file_path,
                'name' => $file->getClientOriginalName(),
                'created_by' => Auth::user()->id
            ]);
            $info->blob_id = $blob->id;
        }
        if ($info->save()) {
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'data' => $info,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_BAD_REQUEST,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WebInfo  $webInfo
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = WebInfo::query();
        $query->with('image');
        $info = $query->find($id);
        if ($info) {
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'data' => new WebInfoResource($info),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_NOT_FOUND,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WebInfo  $webInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $updated = WebInfo::where('id', $id)
            ->update($request->except('created_at', 'updated_at'));
        if($updated > 0)
        {
            return response()->json([
                'status' => true,
                'code' => Response::HTTP_OK,
                'data' => $id,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code' => Response::HTTP_BAD_REQUEST,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WebInfo  $webInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $result = WebInfo::where('id',$id)->delete();
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => $result > 0,
            'data' => $id,
            'meta' => []
        ]);
    }
}
