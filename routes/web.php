<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\SceneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::resource('scenes', SceneController::class);
    Route::resource('characters', CharacterController::class);
    
    // Rotas adicionais para funcionalidades específicas
    Route::post('scenes/reorder', [SceneController::class, 'reorder'])->name('scenes.reorder');
    Route::post('scenes/{scene}/characters', [SceneController::class, 'addCharacter'])->name('scenes.add-character');
    Route::delete('scenes/{scene}/characters/{character}', [SceneController::class, 'removeCharacter'])->name('scenes.remove-character');

    // Rotas para importação de Excel
    Route::get('/excel', [ExcelController::class, 'index'])->name('excel.index');
    Route::post('/excel/import', [ExcelController::class, 'import'])->name('excel.import');
    Route::get('/excel/{excelData}', [ExcelController::class, 'show'])->name('excel.show');
    Route::delete('/excel/{excelData}', [ExcelController::class, 'destroy'])->name('excel.destroy');
});

require __DIR__.'/auth.php';
