<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return view('homepage.index', [
            'posts' => $posts,
        ]);
    }
}
