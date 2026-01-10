<?php

namespace App\Livewire;

use App\Models\Household;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Onboarding extends Component
{
    public ?Household $household = null;

    public function mount(CurrentHouseholdServiceContract $householdService)
    {
        $this->household = $householdService->getCurrentHousehold();

        if ($this->household) {
            $currentRoute = Route::currentRouteName();

            if ($currentRoute === 'household.settings' && !$this->household->onboarding_configured_household) {
                $this->household->update(['onboarding_configured_household' => true]);
            }

            if ($currentRoute === 'bills' && !$this->household->onboarding_added_bills) {
                $this->household->update(['onboarding_added_bills' => true]);
            }
        }
    }

    public function render()
    {
        if (!$this->household) {
            return <<<'HTML'
            <div></div>
            HTML;
        }

        if ($this->household->onboarding_configured_household && $this->household->onboarding_added_bills) {
            return <<<'HTML'
            <div></div>
            HTML;
        }

        return view('livewire.onboarding');
    }
}
