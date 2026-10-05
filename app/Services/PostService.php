<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function create(array $data, User $author): Post
    {
        return DB::transaction(function () use ($data, $author) {
            $post = Post::create([
                'user_id'     => $author->id,
                'title'       => $data['title'],
                'description' => $data['description'],
            ]);

            $post->categories()->sync($data['categories']);

            $this->syncImages($post, $data['images'] ?? []);

            return $post;
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            $post->update([
                'title'       => $data['title'],
                'description' => $data['description'],
            ]);

            $post->categories()->sync($data['categories']);

            // فقط اگر فایل جدیدی آپلود شد، تصاویر را به‌روز می‌کنیم
            if (!empty($data['images'])) {
                // تصاویر قدیمی را از storage حذف می‌کنیم
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
     * ذخیره‌ی فایل‌های آپلود شده و ثبت در دیتابیس
     *
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