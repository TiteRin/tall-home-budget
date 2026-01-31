<?php

namespace App\Livewire\Home\Movements;

use App\Services\Movement\MovementsService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class MovementsList extends Component
{
    #[Reactive]
    public array $incomes = [];

    private MovementsService $service;

    public function boot(): void
    {
        $this->service = MovementsService::create();
    }

    public function render(): View
    {
        if (!$this->service->hasMembers()) {
            return view('livewire.home.movements.movements-list-empty');
        }

        if (!$this->service->hasBills()) {
            return view('livewire.home.movements.movements-list-empty');
        }

        $this->service->setIncomes($this->incomes);
        $movements = $this->service->toMovements();

        if ($movements->isEmpty()) {
            return view('livewire.home.movements.movements-list-empty');
        }

        return view('livewire.home.movements.movements-list', compact('movements'));
    }
}
