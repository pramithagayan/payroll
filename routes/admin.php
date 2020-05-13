<?php

// Register Twill routes here (eg. Route::module('posts'))
Route::module('payrollUploads');
Route::post('payroll', 'PayrollUploadController@upload');