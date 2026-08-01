<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $featured = BlogPost::whereNotNull('published_at')
            ->where('is_featured', true)
            ->latest('published_at')
            ->first();

        $posts = BlogPost::whereNotNull('published_at')
            ->when($featured, fn ($query) => $query->where('id', '!=', $featured->id))
            ->latest('published_at')
            ->paginate(6);

        return view('blog.index', [
            'featured' => $featured,
            'posts' => $posts,
        ]);
    }

    public function show(BlogPost $blogPost)
    {
        abort_if(is_null($blogPost->published_at), 404);

        return view('blog.show', [
            'post' => $blogPost,
        ]);
    }
}
