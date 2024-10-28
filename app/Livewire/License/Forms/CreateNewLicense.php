<?php

namespace App\Livewire\License\Forms;

use Livewire\Component;
use App\Models\License;
use App\Livewire\License\Forms\LicenseRequestApi;
use App\Livewire\License\Forms\LicenseForms;

class CreateNewLicense extends LicenseForms
{
    public ?string $mode = null;

    public function save()
    {
        $this->validate($this->getValidationRules());

        $this->success['status'] = false;
        $this->error['status'] = false;

        $data = [
            'legal_entity_id' => $this->legalEntity->id,
            'type' => $this->type,
            'issued_by' => $this->issued_by,
            'issued_date' => $this->issued_date,
            'active_from_date' => $this->active_from_date,
            'order_no' => $this->order_no,
            'license_number' => $this->license_number,
            'expiry_date' => $this->expiry_date,
            'what_licensed' => $this->what_licensed,
            'is_primary' => $this->is_primary,
        ];

        $license = License::create($data);

        if ($license->wasRecentlyCreated) {
            if(!config('app.debug')) {
                // reset form fields after saving
                $this->resetForm();
            }

            $res = $this->sendApiRequest($data);

            if(isset($res['id']) && $res['id']) {
                $license->update([
                    'uuid' => $res['id']
                ]);
            }

            $this->success['status'] = true;
            $this->success['message'] = __('Ліцензія створена успішно');
        } else {
            $this->error['status'] = true;
            $this->error['message'] = __('Не вдалося створити ліцензію');
        }

        session()->flash('message', __('Ліцензія створена успішно'));
    }

    protected function resetForm()
    {
        $this->type = '';
        $this->issued_by = '';
        $this->issued_date = '';
        $this->active_from_date = '';
        $this->order_no = '';
        $this->license_number = '';
        $this->expiry_date = '';
        $this->what_licensed = '';
    }

    public function sendApiRequest($data): array
    {
        unset($data['legal_entity_id']);
        return LicenseRequestApi::create($data);
    }
}
