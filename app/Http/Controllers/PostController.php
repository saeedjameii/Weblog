<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
{

    public function __construct(
        private PostService $postService
    ){

    }

    public function home(){
        $latestPosts = Post::with(['user', 'categories'])->latest()->take(4)->get();
        return view('home', compact('latestPosts'));
    }

    public function create()
    {
        $categories = Category::all();
        //$categories = Category::where('type', 'post')
        return view('posts.create', compact('categories'));
    }

    public function createPost(PostRequest $request)
    {
        $data = $request->validated();

        $this->postService->create($data, auth('api')->user());

        return redirect()->route('home')->with('success', 'پست با موفقیت ایجاد شد.');
    }

    public function index(Request $request){
        
        $query = Post::with(['categories', 'user'])->latest();

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
        //$categories = Category::where('type', 'post')

        return view('posts.index', compact('posts', 'categories'));
    }

    public function myPosts(){
        $posts = Post::withoutGlobalScope(SoftDeletingScope::class)->where('user_id', auth('api')->id())->with(['categories'])->latest()->get();
        return view('posts.my-posts', compact('posts'));
    }

    public function show(Post $post){
        $post->load(['categories', 'user']);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {

        Gate::authorize('update', $post);
        
        $categories = Category::all();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(PostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->validated();

        $this->postService->update($post, $data);

        return redirect()->route('posts.show', $post)->with('success', 'پست با موفقیت ویرایش شد.');
    }

    public function destroy(Post $post){
        Gate::authorize('delete', $post);

        try{
            $post->delete();
            return redirect()->route('posts.index')->with('success', 'پست مورد نظر با موفقیت حذف شد.');
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
        $posts = Post::withoutGlobalScope(SoftDeletingScope::class)->whereNotNull('deleted_at')->with('categories', 'user')->latest()->get();
        return view('posts.trashed', compact('posts'));
    }
}
