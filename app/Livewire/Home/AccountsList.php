<?php

namespace App\Livewire\Home;

use App\Services\Household\HouseholdServiceContract;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class AccountsList extends Component
{
    public Collection $members;

    /**
     * @throws Exception
     */
    public function mount(HouseholdServiceContract $householdService): void
    {
        if (!$householdService->getCurrentHousehold()) {
            throw new Exception("No household exists");
        }

        $this->members = $householdService->getCurrentHousehold()->members;
    }

    public function render(): View
    {
        if ($this->members->count() === 0) {
            return view('livewire.home.accounts-list-empty');
        }

        return view('livewire.home.accounts-list');
    }
}
