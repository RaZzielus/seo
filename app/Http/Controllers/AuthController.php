<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     @OA\Response(response="200", description="Refresh", @OA\JsonContent()),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          )
     *      )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $token = auth('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);

    }

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     @OA\Response(response="200", description="Refresh", @OA\JsonContent()),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          )
     *      )
     * )
     */
    public function signUp(SignUpRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $userRole = Role::findByName('user', 'api');
        $user->assignRole($userRole);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => getUserResponseData($user),
            'authorization' => [
                'token' =>  auth('api')->login($user),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Logout", @OA\JsonContent())
     * )
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Logout", @OA\JsonContent())
     * )
     */
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => getUserResponseData(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/impersonate",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Impersonate", @OA\JsonContent()),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="number")
     *          )
     *      )
     * )
     */
    public function impersonate(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::find($request->post('id'));

        return response()->json([
            'status' => 'success',
            'message' => 'User was impersonated successfully',
            'user' => getUserResponseData($user),
            'authorization' => [
                'token' =>  auth('api')->login($user),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/revoke-impersonate",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Revoke Impersonate", @OA\JsonContent()),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="number")
     *          )
     *      )
     * )
     */
    public function revokeImpersonate(Request $request): \Illuminate\Http\JsonResponse
    {
        $admin= User::find($request->post('id'));
        $admin->revokePermissionTo('impersonate');

        return response()->json([
            'status' => 'success',
            'message' => 'Admin was revoked from impersonate permissions'
        ]);
    }
}
