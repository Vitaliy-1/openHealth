<?php

use App\Livewire\Division\Division;
use App\Livewire\Division\DivisionForm;
use App\Livewire\Division\HealthcareServiceForm;
use App\Livewire\Employee\EmployeeForm;
use App\Livewire\Employee\EmployeeIndex;
use App\Livewire\LegalEntity\CreateNewLegalEntities;
use App\Livewire\LegalEntity\EditLegalEntity;
use App\Livewire\SearchPatient;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::get('/dashboard/legal-entities/create', CreateNewLegalEntities::class)->name('create.legalEntities');

    Route::group(['middleware' => ['role:Owner']], function () {
        Route::prefix('legal-entities')->group(function () {
            Route::get('/edit', EditLegalEntity::class)->name('edit.legalEntities');
        });
        Route::prefix('division')->group(function () {
            Route::get('/', DivisionForm::class)->name('division.index');
            Route::get('/{division}/healthcare-service', HealthcareServiceForm::class)->name('healthcare_service.index');
        });


        Route::prefix('employee')->group(function () {
            Route::get('/', EmployeeIndex::class)->name('employee.index');
            Route::get('/form/{id?}', EmployeeForm::class)->name('employee.form');
        });
        Route::get('/search/patient', [SearchPatient::class, 'index']);

    });

});
