<?php

namespace App\Livewire\Home\Movements;

use App\Services\Bill\BillsCollection;
use App\Services\Household\CurrentHouseholdServiceContract;
use App\Services\Movement\MovementsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class MovementsList extends Component
{
    #[Reactive]
    public array $incomes = [];

    private function buildService(): MovementsService
    {
        $currentHousehold = app(CurrentHouseholdServiceContract::class)->getCurrentHousehold();
        $service = MovementsService::create()
            ->withMembers($currentHousehold->members)
            ->withBills(new BillsCollection($currentHousehold->bills));

        $service = $service->withIncomes($this->incomes);

        return $service;
    }

    public function getMovementsProperty(): Collection
    {
        return $this->buildService()->toMovements();
    }

    public function render(): View
    {
        if ($this->movements->isEmpty()) {
            return view('livewire.home.movements.movements-list-empty');
        }

        return view('livewire.home.movements.movements-list', [
            'movements' => $this->movements,
        ]);
    }
}
