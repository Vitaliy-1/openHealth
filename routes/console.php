<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('first-run', function () {
    $this->call('key:generate');
    $this->call('migrate');
    $this->call('db:seed', ['--class' => 'RolesPermissionsSeeder']);
//    $this->call('permission:create-role', ['name' => 'Owner']);
})->purpose('Completes the first run of the application');
