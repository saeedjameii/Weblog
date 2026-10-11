<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function create(array $data, User $author, string $status = 'published'): Post
    {
        return DB::transaction(function () use ($data, $author, $status) {
            $post = Post::create([
                'user_id'     => $author->id,
                'title'       => $data['title'],
                'description' => $data['description'],
                'status' => $status,
            ]);

            $post->categories()->sync($data['categories']);

            $this->syncImages($post, $data['images'] ?? []);

            return $post;
        });
    }

    public function update(Post $post, array $data, ?string $status = null): Post
    {
        return DB::transaction(function () use ($post, $data, $status) {
            $updateData = [
                'title'       => $data['title'],
                'description' => $data['description'],
            ];

            if($status !== null){
                $updateData['status'] = $status;
            }

            $post->update($updateData);

            $post->categories()->sync($data['categories']);

            if (!empty($data['images'])) {
                foreach ($post->images as $image) {
                    Storage::disk('public')->delete($image->path);
                }
                $post->images()->delete();

                $this->syncImages($post, $data['images']);
            }

            return $post;
        });
    }

    /**
     * @param  Post  $post
     * @param  array<\Illuminate\Http\UploadedFile>  $images
     */
    private function syncImages(Post $post, array $images): void
    {
        foreach ($images as $image) {
            $path = $image->store('posts', 'public');
            $post->images()->create(['path' => $path]);
        }
    }
}