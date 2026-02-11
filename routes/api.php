<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\BoardController;
use App\Http\Controllers\Api\V1\ListController;
use App\Http\Controllers\Api\V1\CardController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\ActivityController;
use App\Http\Controllers\Api\V1\BoardMemberController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\SearchController;




Route::prefix('v1')->group(function () {

    Route::post('auth/signup', [AuthController::class, 'signup']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:api' )->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('projects/{projectId}/boards', [BoardController::class, 'store']);
        Route::get('boards/{boardId}', [BoardController::class, 'show']);

        Route::post('boards/{boardId}/lists', [ListController::class, 'store']);

        Route::post('lists/{listId}/cards', [CardController::class, 'store']);
        Route::put('cards/{card}/move', [CardController::class, 'move']);



        Route::get('projects', [ProjectController::class, 'index']);
        Route::post('projects', [ProjectController::class, 'store']);
         
        Route::post('cards/{card}/comments', [CommentController::class, 'store']);

        Route::post('uploads/presign', [UploadController::class, 'presign']);

        Route::get('boards/{boardId}/activity', [ActivityController::class, 'index']);
        
        Route::post('boards/{boardId}/members', [BoardMemberController::class, 'store']);
        Route::delete('boards/{boardId}/members/{userId}', [BoardMemberController::class, 'destroy']);

        Route::get('boards/{boardId}/activity', [ActivityController::class, 'index']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('notifications/{id}/read', [NotificationController::class, 'markRead']);
        

        Route::get('search', [SearchController::class, 'index']);

        Route::get( 'projects/{projectId}/boards',[BoardController::class, 'indexByProject']);
        Route::get('projects/{projectId}', [ProjectController::class, 'show']);


    });

});


 