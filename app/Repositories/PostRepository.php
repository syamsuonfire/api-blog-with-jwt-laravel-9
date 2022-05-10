<?php

namespace App\Repositories;

use App\Helpers\UploadHelper;
use App\Models\Post;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Str;

class PostRepository
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    // get paginate latest
    public function getPaginate($perPage): Paginator
    {
        return $this->post->with('user', 'category')->latest()->paginate($perPage);
    }

    // get by id
    public function getById($id): Post|null
    {
        return $this->post->with('user', 'category')->find($id);
    }


    // get by slug
    public function getBySlug($slug) : Post|null
    {
        return $this->post->with('user', 'category')->where('slug', $slug)->first();
    }

    // save post
    public function save($request) : Post
    {
        if (!empty($request->image)){
            $titleShort      = Str::slug(substr($request['title'], 0, 20));
            $request->image = UploadHelper::upload('image', $request['image'], $titleShort . '-' . time(), 'images/posts');
        }

        $post = new $this->post;
        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->content = $request->content;
        $post->headline = $request->headline;
        $post->image = $request->image;
        $post->featured = $request->featured;
        $post->category_id = $request->category_id;
        $post->user_id = auth()->user()->id;
        $post->save();

        return $post->fresh();
    }

    // update post
    public function update($id, $request): Post|null
    {
        $post = $this->post->find($id);

        if (is_null($post)) {
            return null;
        }

        if (!empty($request->image)){
            $titleShort = Str::slug(substr($request['title'], 0, 20));
            $request->image = UploadHelper::update('image', $request['image'], $titleShort . '-' . time(), 'images/posts', $post->image);
        } else {
            $request->image = $post->image;
        }

        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->content = $request->content;
        $post->headline = $request->headline;
        $post->image = $request->image;
        $post->featured = $request->featured;
        $post->category_id = $request->category_id;
        $post->user_id = auth()->user()->id;
        $post->save();

        return $post;
    }

    // delete
    public function delete($id) : bool
    {
        $post = $this->post->find($id);

        if (is_null($post)) {
            return false;
        }
        $post->delete();
        return true;
    }



}
