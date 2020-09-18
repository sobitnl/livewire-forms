<?php

use SobitNL\LivewireForms\Controllers\FileUploadController;

Route::group(['middleware' => 'web'], function () {
    Route::post('/livewire-forms/file-upload', FileUploadController::class)
        ->name('livewire-forms.file-upload');
});
