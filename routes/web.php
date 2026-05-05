<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\CaseFileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LegalSpecialtyController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/session-check', function () {
    if (auth()->check()) {
        // Solo responder: esto ya mantiene viva la sesión
        return response()->json([
            'active' => true
        ]);
    }
    return response()->json([
        'active' => false
    ]);
})->name('session.check');

Route::get('/refresh-csrf', function () {
    return response()->json(['token' => csrf_token()]);
})->name('refresh.csrf');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->get('logs', [LogViewerController::class, 'index']);

Route::middleware(['auth'])->group(function () {
    Route::get('clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('users', UserController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('cases', CaseFileController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions',PermissionController::class);
    Route::resource('legal-specialties', LegalSpecialtyController::class);
    Route::get('legal-specialties-data', [LegalSpecialtyController::class,'data'])->name('legal-specialties.data');
    Route::get('legal-subjects/by-specialty', [LegalSpecialtyController::class, 'bySpecialty'])->name('legal-subjects.by-specialty');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/check-case', [PaymentController::class, 'checkCase'])->name('payments.check-case');
    Route::get('payments/by-installment/{id}', [PaymentController::class, 'byInstallment']);

    Route::get('payments/data', [PaymentController::class, 'data'])->name('payments.data');
    Route::post('payments/delete', [PaymentController::class, 'delete'])->name('payments.delete');

    Route::get('consultations/stats', [ConsultationController::class, 'stats'])->name('consultations.stats');
    Route::get('consultations/check-case', function(Request $request){
        $installment = ConsultationInstallment::find($request->installment_id);
        return [
            'has_case' => $installment->consultation->case ? true : false
        ];
    });
    Route::get('consultations-data', [ConsultationController::class, 'data'])->name('consultations.data');
    Route::post('consultations/{consultation}/generate-case', [ConsultationController::class, 'generateCase'])->name('consultations.generate-case');
    Route::post('consultations/{consultation}/status', [ConsultationController::class, 'changeStatus'])->name('consultations.change-status');
    Route::post('consultations/{consultation}/reject', [ConsultationController::class, 'reject'])->name('consultations.reject');
    Route::resource('consultations', ConsultationController::class);
});

Route::get(
    'ubigeos/search',
    [ClientController::class,'searchUbigeo']
)->name('ubigeos.search');