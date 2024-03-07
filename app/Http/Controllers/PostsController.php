<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Pagination\Paginator;
use Intervention\Image\Laravel\Facades\Image;

//use intervention\Image\Facades\Image;
class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = auth()->user()->following()->pluck('profiles.user_id');

        Paginator::useBootstrap();

        $posts = Post::whereIn('user_id', $users)->with('user')->latest()->Paginate(2);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }
    public function store()
    {
        $data = request()->validate([
            'caption' => 'required',
            'image' => ['required', 'image']
        ]);

        $imagePath = request('image')->store('uploads', 'public');
//        $image = Image::make(public_path("storage/{$imagePath}"))->fit(1200, 1200);
//
//        // Afbeelding opslaan
//        $image->save();

        auth()->user()->posts()->create([
            'caption' => $data['caption'],
            'image' => $imagePath,
        ]);

        return redirect('/profile/' . auth()->user()->id);
    }
    public function show(\App\Models\Post $post)
    {
       return view('posts.show',[
           'post' => $post,
       ]);
    }
}
