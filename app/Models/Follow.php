<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    public function otherUser()
    {
        return $this->belongsTo(User::class, 'otherUser_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}