<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    use ResponseTrait;

    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        // only allow authenticated admin to access these methods
        $this->middleware('admin:api', ['except' => ['index', 'show']]);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->categoryService->getCategoryPaginate($request->perPage);
            return $this->responseSuccess($data, 'Category List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->categoryService->getCategoryById($id);
            if (is_null($data)) {
                return $this->responseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'Category Details Fetch Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->categoryService->saveCategory($request);
            return $this->responseSuccess($data, 'New Category Created Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update($id, Request $request): JsonResponse
    {
        try {
            $data = $this->categoryService->updateCategory($id, $request);
            if (is_null($data))
                return $this->responseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseSuccess($data, 'Category Updated Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data =  $this->categoryService->getCategoryById($id);
            if (empty($data)) {
                return $this->responseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
            }
            $deleted = $this->categoryService->deleteCategory($id);
            if (!$deleted) {
                return $this->responseError(null, 'Failed to delete the category.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $this->responseSuccess($data , 'Category Deleted Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
