<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function before(User $user): bool|null
    {
        if ($user->is_super_admin) {
            return true;
        }
     
        return null;
    }

    public function viewAny(User $user)
    {
        return $user->is_super_admin
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function view(User $user, Comment $model)
    {
        return $model->author->is_private && $user->id !== $model->author->id
            ? Response::denyAsNotFound()
            : Response::allow();
    }

    public function update(User $user, Comment $model)
    {
        return $user->id === $model->id;
    }

    public function delete(User $user, Comment $model)
    {
        return $user->id === $model->user_id;
    }
}
