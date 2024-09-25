<?php

use App\Notifications\InvoiceCreate;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Filament\Notifications\Events\DatabaseNotificationsSent;

Route::get('/', function () {
    return redirect('/admin/login');
});
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

Route::get('/test', function () {
    $recipient = auth()->user();

    try {
        Notification::make()
            ->title('Invoice baru')
            ->body('Invoice baru ditambahkan oleh ' . $recipient->nama_lengkap)
            ->sendToDatabase($recipient);
        // event(new DatabaseNotificationsSent($recipient));
        dd('done send');
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
});
