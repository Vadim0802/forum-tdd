<?php

use App\Http\Controllers\ChannelController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('threads', ThreadController::class)->except(['show', 'index', 'destroy']);

    Route::post('threads/{channel}/{thread}/replies', [ReplyController::class, 'store'])->name('threads.replies.store');
    Route::post('replies/{reply}/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('threads/{channel}/{thread}', [ThreadController::class, 'destroy'])->name('threads.channel.destroy');
});

Route::get('threads', [ThreadController::class, 'index'])->name('threads.index');

Route::get('threads/{channel}', [ChannelController::class, 'index'])->name('threads.channel.index');
Route::get('threads/{channel}/{thread}', [ThreadController::class, 'show'])->name('threads.show');

Route::get('profiles/{user}', [ProfileController::class, 'show'])->name('profiles.show');

require __DIR__.'/auth.php';
