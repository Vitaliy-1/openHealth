<?php

declare(strict_types=1);

namespace App\Livewire\Patient\Records;

use Illuminate\Contracts\View\View;

class PatientEpisodes extends BasePatientComponent
{
    public function render(): View
    {
        return view('livewire.patient.patient-episodes');
    }
}
