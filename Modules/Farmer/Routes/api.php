<?php

use Illuminate\Http\Request;


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

/*Route::middleware('auth:api')->get('/farmer', function (Request $request) {
    return $request->user();
});*/
Route::group(["prefix" => "farmer"], function() {

   Route::resource("category", CategoryController::class);
   Route::get("all_categories", [Modules\Farmer\Http\Controllers\CategoryController::class, 'all']);
   Route::resource("user", FarmerController::class);
   Route::resource("news", NewsController::class);
   Route::resource("medias", MediaController::class);
   Route::get("medias/mediaList/{marketing_content}",  [Modules\Farmer\Http\Controllers\MediaController::class, 'mediaList']);
   Route::get("medias/{slug}/list",  [Modules\Farmer\Http\Controllers\MediaController::class, 'links']);

   Route::resource("media-links", MediaLinkController::class);
   Route::resource("messages", MessageController::class);

   Route::get("auth", [Modules\Farmer\Http\Controllers\FarmerController::class, 'auth']);
   Route::post("auth", [Modules\Farmer\Http\Controllers\FarmerController::class, 'auth']);
   Route::get("login-logs/{id}", [Modules\Farmer\Http\Controllers\LoginLogController::class, 'logs']);



   Route::post("forgot_password", [Modules\Farmer\Http\Controllers\FarmerController::class, 'forgot_password']);


   Route::get("user-login", [Modules\Farmer\Http\Controllers\FarmerController::class, 'getUserLogins']);
   Route::get('fileaccess-log', [Modules\Farmer\Http\Controllers\FileAccessController::class, 'index']);
   Route::post('fileaccess-log', [Modules\Farmer\Http\Controllers\FileAccessController::class, 'saveLog']);
   Route::get('fileaccess-log/count-downloads', [Modules\Farmer\Http\Controllers\FileAccessController::class, 'count_download']);
   Route::get('fileaccess-log/count-shared', [Modules\Farmer\Http\Controllers\FileAccessController::class, 'count_shared']);
   Route::get('fileaccess-log/top-downloads', [Modules\Farmer\Http\Controllers\FileAccessController::class, 'top_downloads']);
   Route::get("fileaccess-log/user_downloads/{user_id}", [Modules\Farmer\Http\Controllers\FileAccessController::class, 'userDownloads']);
   Route::get("fileaccess-log/user_sharing/{user_id}", [Modules\Farmer\Http\Controllers\FileAccessController::class, 'userSharing']);


   Route::get('fileaccess-log/weekly_downloads', [Modules\Farmer\Http\Controllers\FileAccessController::class, 'weeklyDownload']);
   Route::get('fileaccess-log/weekly_shares', [Modules\Farmer\Http\Controllers\FileAccessController::class, 'weeklySharing']);



   Route::get('page-access-log', [Modules\Farmer\Http\Controllers\PageAcessController::class, 'index']);
   Route::post('page-access-log', [Modules\Farmer\Http\Controllers\PageAcessController::class, 'saveLog']);
   Route::get('page-access/count', [Modules\Farmer\Http\Controllers\PageAcessController::class, 'count_access']);
   Route::get('page-access/last-week', [Modules\Farmer\Http\Controllers\PageAcessController::class, 'lastWeekAccess']);
   Route::get('page-access/weekly', [Modules\Farmer\Http\Controllers\PageAcessController::class, 'weeklyAccess']);
   Route::get("page-access/user_access/{user_id}", [Modules\Farmer\Http\Controllers\PageAcessController::class, 'userAccess']);



   Route::get("category/{category_id}/files", [Modules\Farmer\Http\Controllers\CategoryController::class, 'filesInCategory']);
   Route::get("file-category", [Modules\Farmer\Http\Controllers\FilePermissionController::class, 'categoriesWithFile']);
   Route::post("file-category", [Modules\Farmer\Http\Controllers\FilePermissionController::class, 'savePermissions']);
   Route::post("messages/send", [Modules\Farmer\Http\Controllers\MessageController::class, 'send']);

   Route::post('notification/registerDevice', [Modules\Farmer\Http\Controllers\NotificationController::class, 'registerDevice']);
   Route::get('notification/deviceBinding/{device_id}', [Modules\Farmer\Http\Controllers\NotificationController::class, 'getBindingForDeviceId']);
   Route::get('notification/messages/{user_id}', [Modules\Farmer\Http\Controllers\MessageController::class, 'getUserPushNotifications']);
   Route::put('notification/messages/{id}', [Modules\Farmer\Http\Controllers\MessageController::class, 'setViewed']);
   Route::delete('notification/messages/{id}', [Modules\Farmer\Http\Controllers\MessageController::class, 'deleteNotification']);

    Route::get("search-tags", [Modules\Farmer\Http\Controllers\SearchTagsController::class, 'allTags']);
   Route::post("search-tags", [Modules\Farmer\Http\Controllers\SearchTagsController::class, 'addTag']);
   Route::post("search-tags/remove", [Modules\Farmer\Http\Controllers\SearchTagsController::class, 'removeTag']);
   Route::get("search/{terms}", [Modules\Farmer\Http\Controllers\SearchController::class, 'search']);

   Route::group(["prefix" => "fm"], function() {
    Route::get("tree", [Modules\Farmer\Http\Controllers\FileManagerController::class, 'getTree']);
    Route::get("content", [Modules\Farmer\Http\Controllers\FileManagerController::class, 'getContent']);
    Route::get("url", [Modules\Farmer\Http\Controllers\FileManagerController::class, 'getUrl']);
    Route::get("download", [Modules\Farmer\Http\Controllers\FileManagerController::class, 'getdownload']);
   });



});
