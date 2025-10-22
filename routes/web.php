<?php // Make sure this is at the top if the file is empty

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // You can return plain text:
    // return 'This is the Task Manager API Backend Host.'; 

    // Or return JSON (better for consistency):
    return response()->json([
        'message' => 'Task Manager API Backend Host. Ready for API requests.'
    ], 200);
});