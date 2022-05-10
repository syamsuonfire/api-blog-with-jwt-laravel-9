<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\Paginator;

class CategoryRepository
{

    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getPaginate($perPage): Paginator
    {
        return $this->category->latest()->paginate($perPage);
    }

    public function getById($id): Category|null
    {
        return $this->category->find($id);
    }

    public function save($request): Category
    {
        $category = new $this->category;
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->save();

        return $category->fresh();
    }
    public function update($id, $request): Category|null
    {
        $category = $this->category->find($id);

        if (is_null($category)) {
            return null;
        }
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->save();

        return $category;
    }

    public function delete($id): bool
    {
        $category = $this->category->find($id);

        if (is_null($category)) {
            return false;
        }
        $category->delete();
        return true;
    }
}
