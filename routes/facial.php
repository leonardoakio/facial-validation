<?php

Route::group(["prefix" => "facial"], function () {
    Route::post('/verify', [App\Http\Controllers\FacialValidator\FacialValidationController::class, 'validatePhoto']);
});