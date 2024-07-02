<?php

namespace App\Livewire\License;

use Livewire\Component;
use App\Models\License;
use App\Helpers\JsonHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LicenseShow extends Component
{
    public $license;
    public array $licenseTypes = [];
    public string $license_type = '';
    public string $licenseTypeDescription = '';

    public function mount($id)
    {
        $cacheKey = "license_{$id}";
        $legal_entity_id = Auth::user()->legal_entity_id;

        // Check if the license is in the cache
        $this->license = Cache::remember($cacheKey, 60*60, function () use ($id, $legal_entity_id) {
            return License::where('id', $id)
                            ->where('legal_entity_id', $legal_entity_id)
                            ->firstOrFail();
        });

        $dataHelper = JsonHelper::searchValue('DICTIONARIES_PATH', [
            'LICENSE_TYPE',
        ]);

        $dataHelper = JsonHelper::searchValue('DICTIONARIES_PATH', ['LICENSE_TYPE']);
        $licenseTypes = $dataHelper['LICENSE_TYPE'] ?? [];

        $this->license['type_value'] = $licenseTypes[$this->license['type']]
                                    ?? 'LEGAL_ENTITY_' . $this->license['type'] . '_ADDITIONAL_LICENSE_TYPE';
    }

    public function render()
    {
        return view('livewire.license.license-show');
    }

    public function back()
    {
        return redirect()->route('license.index');
    }
}
