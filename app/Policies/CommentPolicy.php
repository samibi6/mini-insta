<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, Comment $comment)
    {
        // Seul l'administateur ou le créateur du commentaire peut supprimer un commentaire
        return $user->id === $comment->user_id;
    }
}
