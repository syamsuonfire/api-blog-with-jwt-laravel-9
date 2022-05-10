<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserPaginate($perPage): Paginator
    {
        $result = $this->userRepository->getPaginate($perPage);
        return $result;
    }

    public function getUserById($id): User|null
    {
        $result = $this->userRepository->getById($id);
        return $result;
    }

    public function saveUser($request): User
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->userRepository->save($request);
        return $result;
        }

    public function updateUser($id, $request): User|null
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // begin transaction
        DB::beginTransaction();
        try {
            $result = $this->userRepository->update($id, $request);
        } catch (Exception $e) {
            // rollback transaction
            DB::rollback();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("Unable to update user data", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        DB::commit();
        return $result;
    }


    public function deleteUser($id): bool
    {
        // begin transaction
        DB::beginTransaction();
        try {
            $result = $this->userRepository->delete($id);
        } catch (Exception $e) {
            // rollback transaction
            DB::rollback();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("Unable to delete user data", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::commit();
        return $result;
    }
}
