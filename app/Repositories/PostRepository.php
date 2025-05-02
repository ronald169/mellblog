<?php

namespace App\Repositories;

use App\Models\{Category, Post, User};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PostRepository
{
    public function getPostsPaginate(?Category $category = null): LengthAwarePaginator
    {

        $query = $this->getBaseQuery()->orderBy('pinned', 'desc')
                ->when($category, fn (Builder $query) => $query->where('category_id', $category->id))
                ->latest();

        return $query->paginate(config('app.pagination'));
    }


    protected function getBaseQuery(): Builder
    {

        return Post::query()
            ->select('id', 'slug', 'image', 'title', 'body as excerpt', 'user_id', 'category_id', 'created_at', 'pinned')
            ->with('user:id,name', 'category')
            ->when(Auth::check(), function (Builder $query) {
                $query->addSelect([
                    'is_favorited' => DB::table('favorites')
                        ->selectRaw('1')
                        ->whereColumn('post_id', 'posts.id')
                        ->where('user_id', Auth::id())
                        ->limit(1)
                ]);
            })
            ->whereActive(true);
    }

    public function getPostBySlug(string $slug): Post
    {
        return Post::with('user:id,name', 'category')
            ->withCount('validComments')
            ->withExists(['favoritedByUsers' => fn ($query) => $query->where('user_id', Auth::id())])
            ->whereSlug($slug)
            ->firstOrFail();
    }

    public function search(string $search): LengthAwarePaginator
    {
        return $this->getBaseQuery()
            ->latest()
            ->where(function ($query) use ($search) {
                $query->where('body', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            })
            ->paginate(config('app.pagination'));
    }

    public function getFavoritePosts(User $user): LengthAwarePaginator
    {
        return $this->getBaseQuery()
            ->whereHas('favoritedByUsers', fn (Builder $query) => $query->where('user_id', $user->id))
            ->latest()
            ->paginate(config('app.pagination'));
    }

    public function generateUniqueSlug(string $slug): string
    {
        $newSlug = $slug;
        $counter = 1;
        while(Post::where('slug', $newSlug)->exists()) {
            $newSlug = $slug . '-' . $counter;
            ++$counter;
        }

        return $newSlug;
    }
}
