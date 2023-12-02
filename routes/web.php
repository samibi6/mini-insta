<?php

use App\Http\Controllers\HomepageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomepageController::class, 'index'])->name('homepage.index');
// Route::get('/', [HomepageController::class, 'index'])->name('homepage.index');
Route::middleware(['auth'])->group(function () {
    Route::resource('posts', PostController::class);
});
// Gestion des commentaires, uniquement pour les utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    // Ajout d'un commentaire
    Route::post('/posts/{post}/comments', [PostController::class, 'addComment'])->name('posts.comments.add');
    // Suppression d'un commentaire
    Route::delete('/posts/{post}/comments/{comment}', [PostController::class, 'deleteComment'])->name('posts.comments.delete');
    // Ajout like
    Route::post('/posts/{post}/likes', [PostController::class, 'like'])->name('posts.likes');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::post('/profile/{user}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
});


require __DIR__ . '/auth.php';