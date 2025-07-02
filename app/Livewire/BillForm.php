<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Models\Member;
use App\Services\BillService;
use Illuminate\View\View;
use Livewire\Component;

class BillForm extends Component
{
    public array $distributionMethods = [];
    public array $householdMembers = [];

    public string $newName = '';
    public float $newAmount = 0;
    public DistributionMethod $newDistributionMethod = DistributionMethod::EQUAL;
    public int|null $newMemberId = null;

    public function render(): View
    {
        return view('livewire.bill-form');
    }
}
