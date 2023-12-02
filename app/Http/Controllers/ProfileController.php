<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        // Les articles publiés par l'utilisateur
        $posts = $user
            ->posts()
            ->where('published_at', '<', now())
            ->withCount('comments')
            ->withCount('likes')
            ->orderByDesc('published_at')
            ->get();

        // Les commentaires de l'utilisateur triés par date de création
        $comments = $user
            ->comments()
            ->orderByDesc('created_at')
            ->get();

        $follows = $user
            ->follow()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $followCount = Follow::query()
            ->where('user_id', Auth::id())
            ->where('otherUser_id', $user->id)
            ->count();

        $totalFollows = $user->followed()->count();
        $totalFollowers = $user->follow()->count();
        // On renvoie la vue avec les données
        return view('profile.show', [
            'user' => $user,
            'posts' => $posts,
            'comments' => $comments,
            'follows' => $follows,
            'followCount' => $followCount,
            'totalFollows' => $totalFollows,
            'totalFollowers' => $totalFollowers
        ]);
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function follow(Request $request, User $user)
    {
        $followCount = Follow::query()
            ->where('user_id', Auth::id())
            ->where('otherUser_id', $user->id)
            ->count();
        if ($followCount === 0) {
            $follow = new Follow();
            $follow->user_id = Auth::id();
            $follow->otherUser_id = $user->id;
            $follow->save();
        } else {
            $follow = Follow::query()
                ->where('user_id', Auth::id())
                ->where('otherUser_id', $user->id)
                ->first();
            $follow->delete();
        };
        // dd($likeCount);
        return redirect()->back();
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // $request->user()->fill($request->validated());

        // if ($request->user()->isDirty('email')) {
        //     $request->user()->email_verified_at = null;
        // }

        // $request->user()->save();
        // dd($request->all());
        $data = $request->validated();
        $user = $request->user();

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->fill($data);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        // Validation de l'image sans passer par une form request
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        // Si l'image est valide, on la sauvegarde
        if ($request->hasFile('avatar')) {
            $user = $request->user();
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $path;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }
}