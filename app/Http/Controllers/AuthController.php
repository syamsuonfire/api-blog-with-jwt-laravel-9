<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ResponseTrait;
    protected $authService;
    protected $userService;
    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $user = $this->userService->saveUser($request);

            $credentials = $request->only('email', 'password');
            if ($user) {
                if ($token = $this->authService->guard()->attempt($credentials)) {
                    $data =  $this->authService->createNewToken($token);
                    return $this->responseSuccess($data, 'User Registered and Logged in Successfully!');
                }
            }
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->responseError(null, $validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $credentials = $request->only('email', 'password');
            if ($token = $this->authService->guard()->attempt($credentials)) {
                $data =  $this->authService->createNewToken($token);
            } else {
                return $this->responseError(null, 'Invalid Email and Password!', Response::HTTP_UNAUTHORIZED);
            }

            return $this->responseSuccess($data, 'Logged In Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(): JsonResponse
    {
    try {
            $this->authService->guard()->logout();
            return $this->responseSuccess(null, 'Logged out successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh(): JsonResponse
    {
    try {
            $data = $this->authService->createNewToken($this->authService->guard()->refresh());
            return $this->responseSuccess($data, 'Token Refreshed Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function userProfile(): JsonResponse
    {
    try {
            $data = $this->authService->guard()->user();
            return $this->responseSuccess($data, 'Profile Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
