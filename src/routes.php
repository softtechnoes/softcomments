<?php

Route::post('comments', config('comments.controller') . '@store');
Route::delete('comments/{comment}', config('comments.controller') . '@destroy');
Route::GET('comments/{comment}', config('comments.controller') . '@update');
Route::post('comments/{comment}', config('comments.controller') . '@reply');
Route::GET('delete-comments/{comment}', config('comments.controller') . '@destroy');
Route::GET('reply-comments/{comment}', config('comments.controller') . '@reply');
