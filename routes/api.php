<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('twilio/members', [App\Http\Controllers\Api\TwilioController::class, 'createMember']);
Route::post('twilio/sms', [App\Http\Controllers\Api\TwilioController::class, 'sendSMS']);
Route::post('twilio/whatsapp', [App\Http\Controllers\Api\TwilioController::class, 'sendWHATSAPP']);
Route::post('twilio/email', [App\Http\Controllers\Api\TwilioController::class, 'sendEmail']);




Route::post('twilio/registerDevice', [App\Http\Controllers\Api\TwilioController::class, 'registerDevice']);
Route::get('twilio/deviceBinding/{device_id}', [App\Http\Controllers\Api\TwilioController::class, 'getBindingForDeviceId']);

Route::get('content', [App\Http\Controllers\Api\FileManagerController::class, 'content']);
Route::get('tree', [App\Http\Controllers\Api\FileManagerController::class, 'tree']);
Route::get('download', [App\Http\Controllers\Api\FileManagerController::class, 'download']);
Route::get('url', [App\Http\Controllers\Api\FileManagerController::class, 'url']);
Route::get('contents/{slug}', [App\Http\Controllers\Api\ContentController::class,'category']);
Route::get('content/{id}', [App\Http\Controllers\Api\ContentController::class,'content']);
Route::get('medias/{slug}', [App\Http\Controllers\Api\MediaController::class,'links']);
Route::get('medias', [App\Http\Controllers\Api\MediaController::class,'index']);
Route::get('marketing_content', [App\Http\Controllers\Api\MediaController::class,'marketing_content']);
Route::post('auth', [App\Http\Controllers\Api\UserController::class,'auth']);

Route::resource('users', App\Http\Controllers\Api\UserController::class);
Route::get('user_logins', [App\Http\Controllers\Api\UserController::class, 'getUserLogins']);
Route::get('export/user_logins', [App\Http\Controllers\Api\UserController::class, 'downloadUserLogins']);
Route::get('export/top_downloads',  [App\Http\Controllers\Api\FileAccessLogController::class, 'topDownloads']);
Route::post('register', [App\Http\Controllers\Api\UserController::class, 'registerUser']);
Route::post('forgot_password', [App\Http\Controllers\Api\UserController::class, 'forgot_password']);
Route::post('/peat/image_analysis', [App\Http\Controllers\Api\PeatController::class, 'image_analysis']);

Route::get('file_access_log', [App\Http\Controllers\Api\FileAccessLogController::class, 'index']);

Route::get('export/page_access', [App\Http\Controllers\Api\ReportController::class, 'pageAccessDownload']);
Route::get('export/file_access', [App\Http\Controllers\Api\FileAccessLogController::class, 'downloadlogs']);

Route::post('file_access_log', [App\Http\Controllers\Api\FileAccessLogController::class, 'saveLog']);
Route::get('file_access_log/downloads', [App\Http\Controllers\Api\FileAccessLogController::class, 'count_download']);
Route::get('page_access_log', [App\Http\Controllers\Api\ReportController::class, 'page_access_log']);

Route::group(['prefix' => 'farmer_admin'], function(){
    Route::post("auth", [App\Http\Controllers\Api\UserController::class, 'farmer_admin_auth']);
});


Route::group([
    'middleware' => 'api',
    'prefix'     => 'file-manager',
    'namespace'  => 'Alexusmai\LaravelFileManager\Controllers',
], function () {

    Route::get('initialize', 'FileManagerController@initialize')
        ->name('fm.initialize');

    Route::get('content', 'FileManagerController@content')
        ->name('fm.content');

    Route::get('tree', 'FileManagerController@tree')
        ->name('fm.tree');

    Route::get('select-disk', 'FileManagerController@selectDisk')
        ->name('fm.select-disk');

    Route::post('upload', 'FileManagerController@upload')
        ->name('fm.upload');

    Route::post('delete', 'FileManagerController@delete')
        ->name('fm.delete');

    Route::post('paste', 'FileManagerController@paste')
        ->name('fm.paste');

    Route::post('rename', 'FileManagerController@rename')
        ->name('fm.rename');

    Route::get('download', 'FileManagerController@download')
        ->name('fm.download');

    Route::get('thumbnails', 'FileManagerController@thumbnails')
        ->name('fm.thumbnails');

    Route::get('preview', 'FileManagerController@preview')
        ->name('fm.preview');

    Route::get('url', 'FileManagerController@url')
        ->name('fm.url');

    Route::post('create-directory', 'FileManagerController@createDirectory')
        ->name('fm.create-directory');

    Route::post('create-file', 'FileManagerController@createFile')
        ->name('fm.create-file');

    Route::post('update-file', 'FileManagerController@updateFile')
        ->name('fm.update-file');

    Route::get('stream-file', 'FileManagerController@streamFile')
        ->name('fm.stream-file');

    Route::post('zip', 'FileManagerController@zip')
        ->name('fm.zip');

    Route::post('unzip', 'FileManagerController@unzip')
        ->name('fm.unzip');

    // Route::get('properties', 'FileManagerController@properties');

    // Integration with editors
    Route::get('ckeditor', 'FileManagerController@ckeditor')
        ->name('fm.ckeditor');

    Route::get('tinymce', 'FileManagerController@tinymce')
        ->name('fm.tinymce');

    Route::get('tinymce5', 'FileManagerController@tinymce5')
        ->name('fm.tinymce5');

    Route::get('summernote', 'FileManagerController@summernote')
        ->name('fm.summernote');

    Route::get('fm-button', 'FileManagerController@fmButton')
        ->name('fm.fm-button');
});

