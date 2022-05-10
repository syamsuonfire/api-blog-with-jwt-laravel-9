<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Http\Response;

class PostController extends Controller
{
    use ResponseTrait;

    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
        // $this->middleware('auth:api', ['except' => ['index', 'show', 'showBySlug']]);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $data = $this->postService->getPostPaginate($request->perPage);
            return $this->responseSuccess($data, 'Post List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $data = $this->postService->getPostById($id);
            if (is_null($data)) {
                return $this->responseError(null, 'Post Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'Post Details Fetch Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // show by slug
    public function showBySlug($slug): JsonResponse
    {
        try {
            $data = $this->postService->getPostBySlug($slug);
            if (is_null($data)) {
                return $this->responseError(null, 'Post Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseSuccess($data, 'Post Details Fetch Successfully!');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $this->postService->savePost($request);
            return $this->responseSuccess($data, 'New Post Created Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update($id, Request $request): JsonResponse
    {
        try {
            $data = $this->postService->updatePost($id, $request);
            if (is_null($data))
                return $this->responseError(null, 'Post Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseSuccess($data, 'Post Updated Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $data =  $this->postService->getPostById($id);
            if (empty($data)) {
                return $this->responseError(null, 'Post Not Found', Response::HTTP_NOT_FOUND);
            }
            $deleted = $this->postService->deletePost($id);
            if (!$deleted) {
                return $this->responseError(null, 'Failed to delete the post.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $this->responseSuccess($data , 'Post Deleted Successfully!');
        } catch (\Exception $exception) {
            return $this->responseError(null, $exception->getMessage(), $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

}
