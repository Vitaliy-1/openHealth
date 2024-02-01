<?php

use App\Livewire\Division\Division;
use App\Livewire\Division\DivisionForm;
use App\Livewire\Division\HealthcareServiceForm;
use App\Livewire\Registration\CreateNewLegalEntities;
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
    Route::get('/dashboard/division', DivisionForm::class)->name('division.index');
    Route::get('/dashboard/search/patient', SearchPatient::class);
    Route::get('/dashboard/division/{division}/healthcare-service', HealthcareServiceForm::class)->name('healthcare_service.index');

});
