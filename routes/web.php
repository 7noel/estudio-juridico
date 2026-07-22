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
use App\Http\Controllers\CaseActivityController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AgendaEventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationSettingController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ConsultationFollowUpController;
use App\Http\Controllers\Reports\FinancialReportController;
use App\Http\Controllers\Reports\CollectionReportController;
use App\Http\Controllers\Reports\ProfitabilityReportController;
use App\Http\Controllers\Reports\OperationalReportController;
use App\Http\Controllers\Reports\LawyerReportController;
use App\Http\Controllers\Reports\ClientReportController;
use App\Http\Controllers\Reports\AgendaReportController;
use App\Http\Controllers\Reports\CashReportController;
use App\Http\Controllers\DashboardAgendaController;

use App\Services\WhatsAppService;

Route::get('/test-whatsapp', function () {

    $service = app(WhatsAppService::class);

    return $service->sendText(

        '51945641484',

        'Hola, prueba desde Laravel 12'

    );

});

Route::get('/', function () {
    if(auth()->check()){
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->get('logs', [LogViewerController::class, 'index']);

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::resource('users', UserController::class);
    Route::resource('clients', ClientController::class);
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

    Route::post('/cases/{case}/change-status', [CaseFileController::class, 'changeStatus'])->name('cases.change-status');
    Route::get('cases/data', [CaseFileController::class, 'data'])->name('cases.data');
    Route::get('cases/stats', [CaseFileController::class, 'stats'])->name('cases.stats');
    Route::resource('cases', CaseFileController::class);

    Route::post('/cases/{case}/activities', [CaseActivityController::class, 'store'])->name('cases.activities.store');
    Route::put('/activities/{activity}', [CaseActivityController::class, 'update'])->name('cases.activities.update');
    Route::delete('/activities/{activity}', [CaseActivityController::class, 'destroy'])->name('activities.destroy');

    Route::post('/cases/{case}/documents', [DocumentController::class, 'store'])->name('cases.documents.store');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('cases.documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('cases.documents.destroy');

    Route::post('/cases/{case}/agenda', [AgendaEventController::class, 'store'])->name('cases.agenda.store');
    Route::put('/agenda/{event}', [AgendaEventController::class, 'update'])->name('cases.agenda.store');
    Route::delete('/agenda/{event}', [AgendaEventController::class, 'destroy'])->name('cases.agenda.store');
    Route::get('/cases/{case}/agenda/events', [AgendaEventController::class, 'events']);
    Route::put('/cases/{case}/quick-update', [CaseFileController::class, 'quickUpdate'])->name('cases.quick-update');

    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    Route::resource('follow-ups', ConsultationFollowUpController::class);
});

Route::get('ubigeos/search', [ClientController::class,'searchUbigeo'])->name('ubigeos.search');

Route::middleware(['auth'])->prefix('notification-settings')->name('notification-settings.')->group(function () {
    Route::get('/', [NotificationSettingController::class, 'index'])->name('index');
    Route::get('/create', [NotificationSettingController::class, 'create'])->name('create');
    Route::post('/', [NotificationSettingController::class, 'store'])->name('store');
    Route::get('/{notificationSetting}/edit', [NotificationSettingController::class, 'edit'])->name('edit');
    Route::put('/{notificationSetting}', [NotificationSettingController::class, 'update'])->name('update');
    Route::delete('/{notificationSetting}', [NotificationSettingController::class, 'destroy'])->name('destroy');
    Route::get('/datatable/data', [NotificationSettingController::class, 'datatable'])->name('datatable');
});


Route::prefix('reports')->name('reports.')->group(function () {
    Route::get( 'collection', [CollectionReportController::class, 'index'])->name('collection');
    Route::get( 'collection/datatable', [CollectionReportController::class, 'datatable'])->name('collection.datatable');
    Route::get( 'financial', [FinancialReportController::class, 'index'])->name('financial');
    Route::get( 'financial/datatable', [FinancialReportController::class, 'datatable'])->name('financial.datatable');
    Route::get( 'profitability', [ProfitabilityReportController::class, 'index'])->name('profitability');
    Route::get( 'profitability/datatable', [ProfitabilityReportController::class, 'datatable'])->name('profitability.datatable');
    Route::get( 'operational', [OperationalReportController::class, 'index'])->name('operational');
    Route::get( 'operational/datatable', [OperationalReportController::class, 'datatable'])->name('operational.datatable');
    Route::get( 'lawyers', [LawyerReportController::class, 'index'])->name('lawyers');
    Route::get( 'lawyers/datatable', [LawyerReportController::class, 'datatable'])->name('lawyers.datatable');
    Route::get( 'clients', [ClientReportController::class, 'index'])->name('clients');
    Route::get( 'clients/datatable', [ClientReportController::class, 'datatable'])->name('clients.datatable');
    Route::get( 'agenda', [AgendaReportController::class, 'index'])->name('agenda');
    Route::get( 'agenda/datatable', [AgendaReportController::class, 'datatable'])->name('agenda.datatable');
    Route::get( 'cash', [CashReportController::class, 'index'])->name('cash');
    Route::get( 'cash/datatable', [CashReportController::class, 'datatable'])->name('cash.datatable');
});

Route::prefix('dashboard/agenda')->name('dashboard.agenda.')->group(function () {
    Route::get('events', [DashboardAgendaController::class, 'events'])->name('events');
    Route::post('/', [DashboardAgendaController::class, 'store'])->name('store');
    Route::put('/{event}', [DashboardAgendaController::class, 'update'])->name('update');
    Route::delete('/{event}', [DashboardAgendaController::class, 'destroy'])->name('destroy');
});

Route::get('dashboard/legal-events', [DashboardController::class, 'calendarEvents'])->name('dashboard.legal-events');