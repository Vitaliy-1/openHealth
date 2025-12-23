<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee\EmployeeRequest;
use App\Enums\Employee\RequestStatus;
use App\Models\Declaration;
use App\Models\LegalEntity;
use App\Models\Relations\Party; // Додано
use Illuminate\Http\Request;

class Dashboard extends Component
{
    public ?LegalEntity $legalEntity = null;

    // STATISTICS
    public $hrStats = [];
    public $ownerStats = [];
    public $doctorStats = [];

    // LISTS
    public $recentRequests = [];

    // Debug
    public $viewAs = null;

    public function mount(Request $request, $legalEntity = null): ?\Illuminate\Http\RedirectResponse
    {
        $this->viewAs = $request->query('view_as');

        if ($legalEntity) {
            if (is_numeric($legalEntity) || is_string($legalEntity)) {
                $this->legalEntity = LegalEntity::find($legalEntity);
            } elseif ($legalEntity instanceof LegalEntity) {
                $this->legalEntity = $legalEntity;
            }
        } else {
            $this->legalEntity = legalEntity();
        }

        if (!$this->legalEntity) {
            return redirect()->route('legalEntity.select');
        }

        $this->loadData();
    }

    public function hasAccess(string $role): bool
    {
        if ($this->viewAs) {
            return $this->viewAs === $role;
        }

        $user = Auth::user();

        return match($role) {
            'hr' => $user->can('employee_request:write'),
            'doctor' => $user->can('create-patients'),
            'owner' => $user->can('legal_entity:read'),
            default => false,
        };
    }

    public function loadData(): void
    {
        // --- HR DATA ---
        if ($this->hasAccess('hr')) {
            // 1. New employee requests
            $this->hrStats['pending_count'] = EmployeeRequest::query()
                ->where('legal_entity_id', $this->legalEntity->id)
                ->where('status', RequestStatus::NEW)
                ->count();

            // 2.Unverified Persons (Parties)
            // Looking for Party that has an employee or request in this institution, but is not VERIFIED
            $this->hrStats['unverified_parties'] = Party::query()
                ->where(function($query) {
                    $query->where('verification_status', '!=', 'VERIFIED')
                        ->orWhereNull('verification_status');
                })
                ->whereHas('employees', fn($q) => $q->where('legal_entity_id', $this->legalEntity->id))
                ->count();

            // Recent List Requests
            $this->recentRequests = EmployeeRequest::query()
                ->where('legal_entity_id', $this->legalEntity->id)
                ->where('status', RequestStatus::NEW)
                ->with('party')
                ->latest()
                ->take(5)
                ->get();
        }

        // --- DOCTOR DATA ---
        if ($this->hasAccess('doctor')) {
            $this->doctorStats['appointments_today'] = 8;
            $this->doctorStats['next_patient_time'] = '14:30';
            $this->doctorStats['next_patient_name'] = 'Ковальчук О.І.';

            // Plug for the last patients (to remove the void)
            // In the future, there will be a request to Person::latest()...
            $this->doctorStats['recent_patients'] = [
                ['name' => 'Іваненко Петро', 'reason' => 'Консультація', 'time' => '10:00'],
                ['name' => 'Сидоренко Ганна', 'reason' => 'Вакцинація', 'time' => '11:15'],
                ['name' => 'Бойко Василь', 'reason' => 'Рецепт', 'time' => '12:30'],
            ];
        }

        // --- OWNER DATA ---
        if ($this->hasAccess('owner')) {
            $this->ownerStats['active_doctors'] = $this->legalEntity->employees()
                ->where('employee_type', 'DOCTOR')
                ->where('status', 'APPROVED')
                ->count();

            $this->ownerStats['total_declarations'] = Declaration::query()
                ->where('legal_entity_id', $this->legalEntity->id)
                ->where('status', 'ACTIVE')
                ->count();
        }
    }

    public function render()
    {
        return view('dashboard');
    }
}
