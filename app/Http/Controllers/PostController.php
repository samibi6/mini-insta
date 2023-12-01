<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $posts =
    //         Post::where('published_at', '<', now())
    //         // ->where('body', 'LIKE', '%' . $request->query('search') . '%')
    //         ->orWhere('caption', 'LIKE', '%' . $request->query('search') . '%')
    //         ->orWhereHas('user', function ($query) use ($request) {
    //             $query->where('username', 'LIKE', '%' . $request->query('search') . '%');
    //         })
    //         ->withCount('comments')
    //         ->withCount('likes')
    //         ->orderByDesc('published_at')
    //         ->paginate(12);

    //     return view(
    //         'posts.index',
    //         [
    //             'posts' => $posts,
    //         ]
    //     );
    // }

    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     // Récupérer les IDs des utilisateurs suivis
    //     $usersFollowed = $user->followed()->pluck('otherUser_id');

    //     // Récupérer tous les posts des utilisateurs suivis postés il y a maximum une semaine et les trier par date
    //     $postsFromFollowedUsers = Post::whereHas('user', function ($query) use ($usersFollowed) {
    //         $query->whereIn('id', $usersFollowed);
    //     })
    //         ->where('published_at', '>=', now()->subWeek())
    //         ->orderByDesc('published_at')
    //         ->withCount('comments')
    //         ->withCount('likes');

    //     // Récupérer tous les posts (même ceux des utilisateurs non suivis) et les trier par nombre de likes
    //     $allPosts = Post::select('posts.*')
    //         ->leftJoin('likes', 'likes.post_id', '=', 'posts.id')
    //         ->where('posts.published_at', '>=', now()->subWeek())
    //         ->where(function ($query) use ($request) {
    //             $query->where('caption', 'LIKE', '%' . $request->query('search') . '%')
    //                 ->orWhereHas('user', function ($query) use ($request) {
    //                     $query->where('username', 'LIKE', '%' . $request->query('search') . '%');
    //                 });
    //         })
    //         ->withCount('comments')
    //         ->withCount('likes')
    //         ->groupBy('posts.id')
    //         ->orderByRaw('COUNT(likes.id) DESC');

    //     $paginatedPosts = $postsFromFollowedUsers->union($allPosts)->paginate(12);

    //     return view('posts.index', [
    //         'posts' => $paginatedPosts,
    //     ]);
    // }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Récupérer les IDs des utilisateurs suivis
        $usersFollowed = $user->followed()->pluck('otherUser_id');

        // Récupérer tous les posts des utilisateurs suivis postés il y a maximum une semaine et les trier par date
        $postsFromFollowedUsers = Post::whereHas('user', function ($query) use ($usersFollowed) {
            $query->whereIn('id', $usersFollowed);
        })
            ->where('published_at', '>=', now()->subWeek())
            ->orderByDesc('published_at')
            ->withCount('comments')
            ->withCount('likes');

        // Récupérer tous les posts (même ceux des utilisateurs non suivis) et les trier par nombre de likes
        $allPosts = Post::select('posts.*')
            ->leftJoin('likes', 'likes.post_id', '=', 'posts.id')
            ->where('posts.published_at', '>=', now()->subWeek())
            ->withCount('comments')
            ->withCount('likes')
            ->groupBy('posts.id')
            ->orderByRaw('COUNT(likes.id) DESC');

        // Si une recherche est effectuée, appliquer la recherche à tous les posts, utilisateurs et légendes
        if ($request->query('search')) {
            $postsFromFollowedUsers->where(function ($query) use ($request) {
                $query->where('caption', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhereHas('user', function ($query) use ($request) {
                        $query->where('username', 'LIKE', '%' . $request->query('search') . '%');
                    });
            });

            $allPosts->where(function ($query) use ($request) {
                $query->where('caption', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhereHas('user', function ($query) use ($request) {
                        $query->where('username', 'LIKE', '%' . $request->query('search') . '%');
                    });
            });
        }

        $paginatedPosts = $postsFromFollowedUsers->union($allPosts)->paginate(12);

        return view('posts.index', [
            'posts' => $paginatedPosts,
        ]);
    }


    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     // Récupérer les IDs des utilisateurs suivis
    //     $usersFollowed = $user->follow()->pluck('otherUser_id');

    //     $posts = Post::where(function ($query) use ($request, $usersFollowed) {
    //         $query->where('published_at', '>=', now()->subWeek())
    //             ->whereIn('user_id', $usersFollowed);
    //     })
    //         ->where(function ($query) use ($request) {
    //             $query->where('caption', 'LIKE', '%' . $request->query('search') . '%')
    //                 ->orWhereHas('user', function ($query) use ($request) {
    //                     $query->where('username', 'LIKE', '%' . $request->query('search') . '%');
    //                 });
    //         })
    //         ->withCount('comments')
    //         ->withCount('likes')
    //         ->orderByDesc('published_at')
    //         ->paginate(12);

    //     return view('posts.index', [
    //         'posts' => $posts,
    //     ]);
    // }







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

        $likes = $post
            ->likes()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $likeCount = Like::query()
            ->where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->count();

        $totalLikes = $post->likes()->count();

        return view('posts.show', [
            'post' => $post,
            'comments' => $comments,
            'likes' => $likes,
            'likeCount' => $likeCount,
            'totalLikes' => $totalLikes
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

    public function like(Request $request, Post $post)
    {
        $likeCount = Like::query()
            ->where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->count();
        if ($likeCount === 0) {
            $like = new Like();
            $like->user_id = Auth::id();
            $like->post_id = $post->id;
            $like->save();
        } else {
            $like = Like::query()
                ->where('user_id', Auth::id())
                ->where('post_id', $post->id)
                ->first();
            $like->delete();
        };
        // dd($likeCount);
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
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->back();
    }
}
