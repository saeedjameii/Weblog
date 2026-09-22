<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function create(array $data, User $author): Post
    {
        return DB::transaction(function () use ($data, $author) {
            $post = Post::create([
                'user_id' => $author->id,
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            $post->categories()->sync($data['categories']);

            return $post;
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            $post->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            $post->categories()->sync($data['categories']);

            return $post;
        });
    }
}