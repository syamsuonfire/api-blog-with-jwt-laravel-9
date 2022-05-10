<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class UserRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getPaginate($perPage): Paginator
    {
        return $this->user->latest()->paginate($perPage);
    }

    public function getById($id): User|null
    {
        return $this->user->find($id);
    }

    public function save($request): User
    {
        $user = new $this->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return $user->fresh();
    }

    public function update($id, $request): User|null
    {
        $user = $this->user->find($id);

        if (is_null($user)) {
            return null;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return $user;
    }


    public function delete($id): bool
    {
        $user = $this->user->find($id);

        if (is_null($user)) {
            return false;
        }
        $user->delete();
        return true;
    }
}
