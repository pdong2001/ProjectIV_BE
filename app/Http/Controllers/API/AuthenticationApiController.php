<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class AuthenticationApiController extends Controller
{
    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        $login = $request->only('email', 'password');
        $login['is_admin'] = true;
        if (!Auth::guard('web')->attempt($login))
        {
            return response()->json([
                'code' => Response::HTTP_UNAUTHORIZED,
                'status' => false
            ]);
        }
        /**
         * @var User $user
         */
        $user = Auth::guard('web')->user();
        $user->tokens()->each(function($token) {
            $token->delete();
        });
        $token = $user->createToken($user->name);
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
            'data' => new UserResource($user),
            'meta' => [
                'token' => $token->accessToken,
                'token_expires_at' => $token->token->expires_at
            ]
            
        ]);
    }

    public function logout(Request $request)
    {
        // $this->validate($request, [
        //     'allDevice' => 'required|boolean'
        // ]);
        
        /**
         * @var User $user
         */
        $user = Auth::user();

        // if ($request->allDevice)
        // {
        //     $user->tokens->each(function($token) {
        //         $token->delete();
        //     });
        //     return response()->json([
        //         'code' => Response::HTTP_OK,
        //         'status' => true,
        //     ]);
        // }
        // dd($user);

        $userToken = $user->token();
        $userToken->delete();
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
        ]);
    }
}
