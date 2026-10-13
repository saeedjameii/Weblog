<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{

    public function __construct(
        private PostService $postService
    ){}

    public function home(){
        $latestPosts = Post::published()->with(['user', 'images', 'categories'])->latest('published_at')->latest()->take(4)->get();
        return view('home', compact('latestPosts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('panel.posts.create', compact('categories'));
    }

    public function createPost(PostRequest $request)
    {
        $data = $request->validated();
        $data['published_at'] = $request->published_at;

        $status = $data['action'];
        unset($data['action']);

        $post = $this->postService->create($data, auth('api')->user(), $status);

        if ($status === 'draft') {
            $message = 'پیش‌نویس با موفقیت ذخیره شد';
        } elseif ($post->isScheduled()) {
            $message = 'خبر با موفقیت زمان‌بندی شد و در تاریخ مشخص‌شده منتشر خواهد شد.';
        } else {
            $message = 'پست با موفقیت منتشر شد';
        }

        return redirect()->route('panel.posts.mine')->with('success', $message);
    }

    public function index(Request $request){
        
        $query = Post::published()->with(['categories', 'images', 'user'])->latest('published_at')->latest();

        if($request->filled('search')){
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });
        }

        if($request->filled('category_id')){
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $posts = $query->paginate(8)->withQueryString();

        $categories = Category::all();

        return view('posts.index', compact('posts', 'categories'));
    }

    public function myPosts(){
        $posts = Post::withoutGlobalScope(SoftDeletingScope::class)
            ->where('user_id', auth('api')->id())
            ->with(['categories', 'images'])
            ->latest()
            ->get();
        return view('panel.posts.my-posts', compact('posts'));
    }

    public function show(Post $post){
        $isAuthor = auth('api')->check() && auth('api')->id() === $post->user_id;

        abort_unless($post->isPublished() || $isAuthor, 404, 'پست مورد نظر یافت نشد');

        $post->load(['categories', 'images', 'user']);

        return view('posts.show', compact('post'));
    }

    public function PanelShow(Post $post){
        Gate::authorize('update', $post);

        $post->load(['categories', 'images', 'user']);

        return view('panel.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = Category::all();

        $post->load('images');

        return view('panel.posts.edit', compact('post', 'categories'));
    }

    public function update(PostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->validated();
        $data['published_at'] = $request->published_at;

        $status = $data['action'];
        unset($data['action']);

        $wasDraft = $post->status === 'draft';
        
        $post = $this->postService->update($post, $data, $status);

        if ($status === 'draft') {
            $message = $wasDraft ? 'پیش‌نویس با موفقیت ذخیره شد' : 'پست با موفقیت به پیش‌نویس منتقل شد';
            return redirect()->route('panel.posts.mine')->with('success', $message);
        }

        if ($post->isScheduled()) {
            $message = 'خبر با موفقیت زمان‌بندی شد و در تاریخ مشخص‌شده منتشر خواهد شد.';
            return redirect()->route('panel.posts.mine')->with('success', $message);
        }

        $message = $wasDraft ? 'پست با موفقیت منتشر شد' : 'پست با موفقیت به‌روزرسانی شد';
        return redirect()->route('panel.posts.show', $post)->with('success', $message);
    }

    public function destroy(Post $post){
        Gate::authorize('delete', $post);

        try{
            $post->delete();
            return redirect()->route('panel.posts.mine')->with('success', 'پست مورد نظر با موفقیت حذف شد.');
        } catch(\Throwable $e){
            return redirect()->back()->with('error', 'پست شما حذف نشد لطفا دوباره تلاش کنید');
        }
    }

    public function restore($id){
        $post = Post::withoutGlobalScope(SoftDeletingScope::class)->findOrFail($id);

        Gate::authorize('restore', $post);
        
        if(!$post->trashed()){
            return back()->withErrors([
                'post' => 'این پست حذف نشده است'
            ]);
        }

        $post->restore();
        return back()->with('success', 'پست موردنظر با موفقیت بازیابی شد');
    }

    public function trashed(){
        $posts = Post::withoutGlobalScope(SoftDeletingScope::class)
            ->whereNotNull('deleted_at')
            ->with(['categories', 'user'])
            ->latest()
            ->get();
        return view('panel.posts.trashed', compact('posts'));
    }
}
