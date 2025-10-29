<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\MemberController;

Route::redirect('/', '/boards');

//게시판
Route::get('/boards/{multi?}', [BoardController::class, 'index'])->name('boards.index');
Route::get('/boards/show/{id}/{page}', [BoardController::class, 'show'])->name('boards.show');

Route::middleware('auth') -> group(function (){
    Route::get('/boards/write/{multi}/{bid?}', [BoardController::class, 'write'])->name('boards.write');
    Route::post('/boards/create', [BoardController::class, 'create'])->name('boards.create');
    Route::post('/boards/saveimage', [BoardController::class, 'saveimage'])->name('boards.saveimage');
    Route::post('/boards/deletefile', [BoardController::class, 'deletefile'])->name('boards.deletefile');
    Route::get('/boards/imgpop/{imgfile}', [BoardController::class, 'imgpop'])->name('boards.imgpop');
    Route::post('/boards/update', [BoardController::class, 'update'])->name('boards.update');
    Route::get('/boards/delete/{bid}/{page}', [BoardController::class, 'delete'])->name('boards.delete');
    Route::get('/boards/summernote/{multi}/{bid?}', [BoardController::class, 'summernote'])->name('boards.summernote');
    Route::post('/boards/memoup', [BoardController::class, 'memoup'])->name('boards.memoup');
    Route::post('/boards/memomodi', [BoardController::class, 'memomodi'])->name('boards.memomodi');
    Route::post('/boards/memomodifyup', [BoardController::class, 'memomodifyup'])->name('boards.memomodifyup');
    Route::post('/boards/memodeletefile', [BoardController::class, 'memodeletefile'])->name('boards.memodeletefile');
    Route::post('/boards/memodelete', [BoardController::class, 'memodelete'])->name('boards.memodelete');
});


//회원
Route::get('/login', [MemberController::class, 'login'])->name('login');
Route::get('/signup', [MemberController::class, 'signup'])->name('auth.signup');
Route::post('/signupok', [MemberController::class, 'signupok'])->name('auth.signupok');
Route::post('/emailcheck', [MemberController::class, 'emailcheck'])->name('auth.emailcheck');
Route::post('/loginok', [MemberController::class, 'loginok']) -> name('auth.loginok');
Route::post('/logout', [MemberController::class, 'logout']) -> name('auth.logout');