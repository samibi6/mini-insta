<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $posts =
            Post::where('published_at', '<', now())
            // ->where('body', 'LIKE', '%' . $request->query('search') . '%')
            ->orWhere('caption', 'LIKE', '%' . $request->query('search') . '%')
            ->orWhereHas('user', function ($query) use ($request) {
                $query->where('username', 'LIKE', '%' . $request->query('search') . '%');
            })
            ->withCount('comments')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view(
            'posts.index',
            [
                'posts' => $posts,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        $post = Post::make();
        $post->caption = $request->validated()['caption'];
        $post->published_at = $request->validated()['published_at'];
        $post->user_id = Auth::id();
        $path = $request->file('img')->store('posts', 'public');
        $post->img_path = $path;
        $post->save();

        return redirect()->route('posts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // On récupère les commentaires de l'article, avec les utilisateurs associés (via la relation)
        // On les trie par date de création (le plus ancien en premier)
        $comments = $post
            ->comments()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('posts.show', [
            'post' => $post,
            'comments' => $comments,
        ]);
    }

    public function addComment(Request $request, Post $post)
    {
        // On vérifie que l'utilisateur est authentifié
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        // On crée le commentaire
        $comment = $post->comments()->make();
        // On remplit les données
        $comment->body = strip_tags($request->input('body'));
        $comment->user_id = auth()->user()->id;
        // On sauvegarde le commentaire
        $comment->save();

        // On redirige vers la page de l'article
        return redirect()->back();
    }

    public function deleteComment(Post $post, Comment $comment)
    {
        // On vérifie que l'utilisateur à le droit de supprimer le commentaire
        $this->authorize('delete', $comment);

        // On supprime le commentaire
        $comment->delete();

        // On redirige vers la page de l'article
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->back();
    }
}
