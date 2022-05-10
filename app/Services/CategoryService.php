<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\CategoryRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CategoryService
{

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategoryPaginate($perPage): Paginator
    {
        $result = $this->categoryRepository->getPaginate($perPage);
        return $result;
    }

    public function getCategoryById($id): Category|null
    {
        $result = $this->categoryRepository->getById($id);
        return $result;
    }

    // save category
    public function saveCategory($request): Category
    {
        $request['slug'] = Str::slug($request->name, '-');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'slug' => 'required|string|between:2,100|unique:categories',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->categoryRepository->save($request);
        return $result;
    }

    // update category
    public function updateCategory($id, $request): Category|null
    {
        $request['slug'] = Str::slug($request->name, '-');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'slug' => 'required|string|between:2,100|unique:categories',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // begin transaction
        DB::beginTransaction();
        try {
            $result = $this->categoryRepository->update($id, $request);
        } catch (Exception $e) {
            // rollback transaction
            DB::rollback();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("Unable to update category data", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        DB::commit();
        return $result;
    }

    // delete category
    public function deleteCategory($id): bool
    {
        // begin transaction
        DB::beginTransaction();
        try {
            $result = $this->categoryRepository->delete($id);
        } catch (Exception $e) {
            // rollback transaction
            DB::rollback();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("Unable to delete category data", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        DB::commit();
        return $result;
    }
}
