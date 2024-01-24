<?php

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



Auth::routes(['register' => false]);

Route::get('/', function () {
    if (Auth::check()) {
        return view('admin');
    }
    return redirect('/login');
});

Route::group(['middleware' => ['auth']], function(){
    Route::get('/dashboard', function (){
        return view('admin');
    });
    Route::get('/file_manager', function (){
        return view('file-manager');
    });
    Route::get('/communications', function (){
        return view('admin');
    });
    Route::get('/users/types', function (){
        return view('admin');
    });
    Route::get('/users/index', function (){
        return view('admin');
    });
    Route::get('/users/permissions', function (){
        return view('admin');
    });
    Route::get('/communications', function (){
        return view('admin');
    });
    Route::get('/content-manager', function (){
        return view('admin');
    });
    Route::get('/media-manager', function (){
        return view('admin');
    });
    Route::get('/file-links', function (){
        return view('admin');
    });
    Route::resource('roles',App\Http\Controllers\RoleController::class);
    Route::resource('permissions',App\Http\Controllers\PermissionController::class);
    Route::resource('user',App\Http\Controllers\UserController::class);
    Route::resource('content',App\Http\Controllers\ContentController::class);
    Route::resource('medias',App\Http\Controllers\MediaController::class);
    Route::resource('media-links',App\Http\Controllers\MediaLinksController::class);
    Route::get('links/latest', [App\Http\Controllers\MediaLinksController::class, 'latest']);
    Route::get('fm/latest_files', [App\Http\Controllers\FileManagerController::class, 'latest_files']);



    //Route::get('permissions', [App\Http\Controllers\PermissionController::class, 'index']);

    Route::get('acl/roles', [App\Http\Controllers\AclRulesController::class, 'getRole']);
    Route::post('acl/roles', [App\Http\Controllers\AclRulesController::class, 'postRules']);
    Route::get('messages', [App\Http\Controllers\MessageController::class, 'getMessages']);
    Route::post('messages', [App\Http\Controllers\MessageController::class, 'postMessages']);
    Route::get('messages/scheduled', [App\Http\Controllers\MessageController::class, 'scheduled']);
    Route::get('messages/past', [App\Http\Controllers\MessageController::class, 'past_messages']);
    Route::get('none_admin', [App\Http\Controllers\RoleController::class, 'none_admin']);
    Route::get('categories', [App\Http\Controllers\CategoryController::class, 'index']);
    Route::get('file-manager/count', [App\Http\Controllers\FileManagerController::class, 'countFiles']);
    Route::resource('fm-links',App\Http\Controllers\FileManagerLinkController::class);
    Route::get('directory-list',[App\Http\Controllers\FileManagerLinkController::class, 'getDirectoryTree']);







});






Route::get('/forgot-password', function (){
   return view('auth.login');
});
Route::get('/recover-password', function (){
    return view('auth.login');
});


Route::post('verify-email', [App\Http\Controllers\UserController::class, 'verifyEmail']);
Route::post('reset-password', [App\Http\Controllers\UserController::class, 'resetPassword']);
Route::get('reset_password', [App\Http\Controllers\UserController::class, 'resetPassword']);
Route::post('reset_password', [App\Http\Controllers\UserController::class, 'resetPassword']);
Route::post('auth', [App\Http\Controllers\UserController::class, 'auth']);
Route::get('verify_email', [App\Http\Controllers\UserController::class, 'verify_email']);





//Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
/*Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles',App\Http\Controllers\RoleController::class);
    Route::resource('users',App\Http\Controllers\UserController::class);
    Route::resource('products',App\Http\Controllers\ProductController::class);
});*/
