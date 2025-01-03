<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Article $article): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return $user->tokenCan('article:create');
    }

    public function update(User $user, Article $article): bool
    {
        return $user->is($article->author) && $user->tokenCan('article:update');
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->is($article->author) && $user->tokenCan('article:delete');
    }

    public function restore(User $user, Article $article): bool
    {
        return false;
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return false;
    }
}
