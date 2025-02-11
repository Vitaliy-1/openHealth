<?php

use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Contract\ContractForm;
use App\Livewire\Contract\ContractIndex;
use App\Livewire\Declaration\DeclarationIndex;
use App\Livewire\Division\DivisionForm;
use App\Livewire\Division\DivisionIndex;
use App\Livewire\Division\HealthcareServiceForm;
use App\Livewire\Employee\EmployeeCreate;
use App\Livewire\Employee\EmployeeEdit;
use App\Livewire\Employee\EmployeeIndex;
use App\Livewire\Encounter\EncounterCreate;
use App\Livewire\LegalEntity\LegalEntities;
use App\Livewire\License\LicenseIndex;
use App\Livewire\License\LicenseShow;
use App\Livewire\License\Forms\LicenseForms;
use App\Livewire\License\Forms\CreateNewLicense;
use App\Livewire\LegalEntity\EditLegalEntity;
use App\Livewire\Patient\PatientForm;
use App\Livewire\Patient\PatientTabs;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailController;
use App\Livewire\Patient\PatientIndex;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');

Route::get('/ehealth/oauth/', [oAuthEhealth::class, 'callback'])->name('ehealth.oauth.callback');
//Route::get('/login', [LoginController::class, 'index'])->middleware('guest')->name('index.login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
//    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard/legal-entities/create', LegalEntities::class)->name('create.legalEntities');

    Route::group(['middleware' => ['role:OWNER|ADMIN'], 'prefix' => 'dashboard'], function () {
        Route::prefix('legal-entities')->group(function () {
            Route::get('/edit/{id?}', EditLegalEntity::class)->name('edit.legalEntities');
        });

        Route::prefix('division')->group(function () {
            Route::get('/', DivisionIndex::class)->name('division.index');
            Route::get('/form/{id?}', DivisionForm::class)->name('division.form');
            Route::get('/{division}/healthcare-service', HealthcareServiceForm::class)->name('healthcare_service.index');
        });

        Route::prefix('employee')->group(function () {
            Route::get('/', EmployeeIndex::class)->name('employee.index');
            Route::get('/{id}', EmployeeEdit::class)->name('employee.edit');
            Route::get('/create', EmployeeCreate::class)->name('employee.create');
        });

        Route::prefix('contract')->group(function () {
            Route::get('/', ContractIndex::class)->name('contract.index');
            Route::get('/form/{id?}', ContractForm::class)->name('contract.form');
        });

        Route::prefix('license')->group(function () {
            Route::get('/', LicenseIndex::class)->name('license.index');
            Route::get('/update/{id}', LicenseForms::class)->name('license.form');
            Route::get('/create', CreateNewLicense::class)->name('license.create');
            Route::get('/show/{id}', LicenseShow::class)->name('license.show');
        });

        Route::prefix('declaration')->group(function () {
            Route::get('/', DeclarationIndex::class)->name('declaration.index');
        });

        Route::get('/test-license', [HomeController::class, 'test']);
    });

    Route::group(['middleware' => ['role:OWNER|ADMIN|DOCTOR']], function () {
        Route::prefix('patient')->group(function () {
            Route::get('/', PatientIndex::class)->name('patient.index');
            Route::get('/create/{id?}', PatientForm::class)->name('patient.form');
            Route::get('/{id}/tab/{tab}', PatientTabs::class)
                ->name('patient.tabs')
                ->where('tab', 'patient-data|summary|episodes');

            Route::prefix('encounter')->group(function () {
                Route::get('/create', EncounterCreate::class)->name('encounter.form');
            });
        });
    });

    Route::get('/{any}', function () {
        return view('errors.404');
    })->where('any', '.*');
});
