<?php

namespace App\Livewire\License\Forms;

use App\Models\LegalEntity;
use Livewire\Component;
use App\Models\License;
use App\Traits\FormTrait;
use App\Livewire\License\Forms\LicenseRequestApi;
use App\Helpers\JsonHelper;

class LicenseForms extends Component
{
    use FormTrait;

    public ?int $license_id = null;
    public ?string $license_uuid = null;
    public ?int $license_order_no = null;
    public ?string $mode = 'edit';
    public ?int $user_id = null;
    public LegalEntity $legalEntity;
    public string $type = '';
    public string $selected_type = '';
    public string $issued_by = '';
    public string $issued_date = '';
    public string $active_from_date = '';
    public string $order_no = '';
    public ?string $license_number = null;
    public ?string $expiry_date = null;
    public string $what_licensed = '';
    public bool $is_primary = false;

    public ?array $dictionaries = [];

    public array $success = [
        'message' => '',
        'status'  => false,
    ];

    public ?array $error = [
        'message' => '',
        'status'  => false,
    ];

    public function mount($id = null)
    {
        if (isset($id) && $id !== null) {
            $this->license_id = $id;
        }

        if ($this->license_id) {
            $license = License::find($this->license_id);
            $this->type = $license->type;
            $this->issued_by = $license->issued_by;
            $this->issued_date = $license->issued_date;
            $this->active_from_date = $license->active_from_date;
            $this->order_no = $license->order_no;
            $this->license_number = $license->license_number;
            $this->expiry_date = $license->expiry_date;
            $this->what_licensed = $license->what_licensed;

            if (isset($license->uuid) && $license->uuid !== null) {
                $this->license_uuid = $license->uuid;
            }
        }

        $this->user_id = auth()->user()->id;
        $this->legalEntity = auth()->user()->legalEntity;
        $this->dictionaries = $this->loadLicenseType();

        //! for debug
        if (config('app.debug')) {
            $this->license_order_no = '1234567';
            $this->type = 'MSP';
            $this->issued_by = 'Кваліфікацйна комісія';
            $this->issued_date = '2022-02-28';
            $this->active_from_date = '2022-02-28';
            $this->order_no = 'ВА43234';
            $this->license_number = 'f1123443';
            $this->expiry_date = '2026-02-28';
            $this->what_licensed = 'реалізація наркотичних засобів';
        }
    }

    public function loadLicenseType(): array
    {
        $dataHelper = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'LICENSE_TYPE',
        ]);
        $licenseTypes = $dataHelper['LICENSE_TYPE'];
        return [
            'licenseTypes' => [
                $licenseTypes
            ],
        ];
    }

    protected function getValidationRules(): array
    {
        $expiry_date_type = $this->type === 'PHARMACY_DRUGS' ? 'required' : 'nullable';
        $expiry_date_check = $this->expiry_date === null ? '' : 'before_or_equal:expiry_date';

        return [
            'type'             => 'required|string',
            'issued_by'        => 'required|string',
            'issued_date'      => 'required|date|before_or_equal:active_from_date',
            'active_from_date' => "required|date|{$expiry_date_check}",
            'order_no'         => 'required|string',
            'license_number'   => 'nullable|string',
            'expiry_date'      => "{$expiry_date_type}|date|after_or_equal:active_from_date",
            'what_licensed'    => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate($this->getValidationRules());
        $this->success['status'] = false;
        $this->error['status'] = false;

        $data = [
            'type'             => $this->type,
            'issued_by'        => $this->issued_by,
            'issued_date'      => $this->issued_date,
            'active_from_date' => $this->active_from_date,
            'order_no'         => $this->order_no,
            'license_number'   => $this->license_number,
            'expiry_date'      => $this->expiry_date,
            'what_licensed'    => $this->what_licensed,
            'is_primary'       => $this->is_primary,
        ];

        if ($this->license_id) {
            $license = License::find($this->license_id);

            if ($license) {
                if ($license->is_primary) {
                    unset($data['is_primary']);
                }

                $updated = $license->update($data);

                if ($updated) {
                    $this->success['status'] = true;
                    $this->success['message'] = __('Ліцензія оновлена успішно');
                    session()->flash('message', __('Ліцензія оновлена успішно'));

                    $res = $this->sendApiRequest($data);

                    if (!$this->license_uuid && $res['id']) {
                        $license->update([
                            'uuid' => $res['id']
                        ]);
                    }

                } else {
                    $this->error['status'] = true;
                    $this->error['message'] = __('Помилка оновлення ліцензії');
                    session()->flash('error', __('Помилка оновлення ліцензії'));
                }
            } else {
                $this->error['status'] = true;
                $this->error['message'] = __('Ліцензія не знайдена');
                session()->flash('error', __('Ліцензія не знайдена'));
            }
        }
    }

    public function render()
    {
        return view('livewire.license.license-forms');
    }

    public function sendApiRequest($data): array
    {
        if ($this->license_uuid) {
            return LicenseRequestApi::update($this->license_uuid, $data);
        }

        return LicenseRequestApi::create($data);
    }

    public function back()
    {
        return redirect()->route('license.index');
    }


}
