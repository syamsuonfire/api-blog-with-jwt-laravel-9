<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use ResponseTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('admin:api');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->userService->getUserPaginate($request->perPage);
            return $this->responseSuccess($data, 'User List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->userService->getUserById($id);
            if (is_null($data)) {
                return $this->responseError(null, 'User Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'User Details Fetch Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->userService->saveUser($request);
            return $this->responseSuccess($data, 'New User Created Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update($id, Request $request): JsonResponse
    {
        try {
            $data = $this->userService->updateUser($id, $request);
            if (is_null($data))
                return $this->responseError(null, 'User Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseSuccess($data, 'User Updated Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data =  $this->userService->getUserById($id);
            if (empty($data)) {
                return $this->responseError(null, 'User Not Found', Response::HTTP_NOT_FOUND);
            }
            $deleted = $this->userService->deleteUser($id);
            if (!$deleted) {
                return $this->responseError(null, 'Failed to delete the user.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $this->responseSuccess($data , 'User Deleted Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
