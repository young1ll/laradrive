<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(\App\Http\Controllers\FileController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/my-files/{folder?}', 'myFiles') // 폴더, 파일 보기
            ->where('folder', '(.*)')
            ->name('myFiles');

        Route::get('/trash', 'trash')->name('trash'); // 삭제된 폴더, 파일 보기(softdelete)

        Route::post('/folder/create', 'createFolder')->name('folder.create'); // 폴더 생성
        Route::post('/file', 'store')->name('file.store'); // 파일 저장
        Route::delete('/file', 'destroy')->name('file.delete'); // 파일 삭제
        Route::delete('/file/delete-forever', 'deleteForever')->name('file.deleteForever'); // 파일 영구 삭제
        Route::post('/file/restore', 'restore')->name('file.restore'); // 파일 복구

        Route::post('/file/add-to-favorite', 'addToFavorites')->name('file.addToFavorites'); // 파일 즐겨찾기 추가

        Route::post('/file/share', 'share')->name('file.share');
        Route::get('/file/shared-with-me', 'sharedWithMe')->name('file.sharedWithMe');
        Route::get('/file/shared-by-me', 'sharedByMe')->name('file.sharedByMe');

        Route::get('/file/download', 'download')->name('file.download'); // 파일 다운로드
        Route::get('/file/download-shared-with-me', 'downloadSharedWithMe')->name('file.downloadSharedWithMe');
        Route::get('/file/download-shared-by-me', 'downloadSharedByMe')->name('file.downloadSharedByMe');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
