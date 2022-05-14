<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationApiController extends Controller
{
    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        $users = User::query()->where('email', $request['email'])->where('is_admin', true);
        /**
         * @var User $user
         */
        $user = $users->first();

        if ($user == null || !Hash::check($request['password'], $user->password)) {
            return response()->json([
                'code' => Response::HTTP_UNAUTHORIZED,
                'status' => false
            ]);
        }
        $token = $user->createToken($user->name);
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
            'data' => new UserResource($user),
            'meta' => [
                'token' => $token->accessToken,
            ]

        ]);
    }

    public function logout(Request $request)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        $userToken = $user->token();
        $userToken->delete();
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        $users = User::query()->where('email', $request['email']);
        /**
         * @var User $user
         */
        $user = $users->first();

        if ($user == null || !Hash::check($request['password'], $user->password)) {
            return response()->json([
                'code' => Response::HTTP_UNAUTHORIZED,
                'status' => false
            ]);
        }
        $token = $user->createToken($user->name);
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
            'data' => new UserResource($user),
            'meta' => [
                'token' => $token->accessToken,
            ]
        ]);
    }

    public function register(Request $request)
    {
        $this->validate($request, User::RULES);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => 0,
        ]);
        $customer = Customer::create([
            'name' => $user->name,
            'address' => '',
            'phone_number' => '',
            'debt' => 0,
            'user_id' => $user->id,
            'created_by' => $user->id
        ]);
        if ($user)
        {
            return response()->json([
                'code' => Response::HTTP_OK,
                'status' => true
            ]);
        }
        return response()->json([
            'code' => Response::HTTP_BAD_REQUEST,
            'status' => false
        ]);
    }
}
