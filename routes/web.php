<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::get('/posts/create', [PostsController::class, 'create'])/*->middleware(['auth'])*/; //(middleware)로그인을 하지 않고 접근할 시 로그인 페이지로, 다시 로그인을 하면 원래 그 페이지(create)로 이동한다
Route::post('/posts/store', [PostsController::class, 'store'])->name('posts.store')/*->middleware(['auth'])*/;
Route::get('/posts/index', [PostsController::class, 'index'])->name('posts.index');

Route::get('/posts/show/{id}', [PostsController::class, 'show'])->name('post.show'); //id 값을 넘겨 주기 위해서 {id}를 쓴다(라우터 파라미터)
Route::get('/posts/mypost', [PostsController::class, 'myposts'])->name('posts.mypost');

Route::get('/posts/{post}', [PostsController::class, 'edit'])->name('post.edit');
Route::put('/posts/{id}', [PostsController::class, 'update'])->name('post.update');
Route::delete('/posts/{id}', [PostsController::class, 'destroy'])->name('post.delete');

Route::get('/chart/index', [ChartController::class, 'index']);
