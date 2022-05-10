<?php

namespace App\Services;

use App\Helpers\UploadHelper;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\PostRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getPostPaginate($perPage): Paginator
    {
        $result = $this->postRepository->getPaginate($perPage);
        return $result;
    }

    public function getPostById($id): Post|null
    {
        $result = $this->postRepository->getById($id);
        return $result;
    }

    public function getPostBySlug($slug): Post|null
    {
        $result = $this->postRepository->getBySlug($slug);
        return $result;
    }

    public function savePost($request): Post
    {
        $request['slug'] = Str::slug($request->title, '-');
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'slug' => 'required|string|between:2,1000|unique:posts',
            'content' => 'required|string|between:2,1000',
            'headline' => 'required',
            'featured' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $this->postRepository->save($request);
        return $result;
    }

    public function updatePost($id, $request): Post|null
    {
        $request['slug'] = Str::slug($request->title, '-');
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:2,100',
            'slug' => 'required|string|between:2,1000|unique:posts',
            'content' => 'required|string|between:2,1000',
            'headline' => 'required',
            'featured' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // begin transaction
        DB::beginTransaction();
        try {
            $result = $this->postRepository->update($id, $request);
        } catch (Exception $e) {
            // rollback transaction
            DB::rollback();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("Unable to update post data", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        DB::commit();
        return $result;
    }

    public function deletePost($id): bool
    {
        // begin transaction
        DB::beginTransaction();
        try {
            $result = $this->postRepository->delete($id);
        } catch (Exception $e) {
            // rollback transaction
            DB::rollback();
            Log::info($e->getMessage());
            throw new InvalidArgumentException("Unable to delete post data", Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        DB::commit();
        return $result;
    }
}
