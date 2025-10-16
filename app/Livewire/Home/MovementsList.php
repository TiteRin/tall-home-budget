<?php

namespace App\Livewire\Home;

use App\Services\Movement\MovementsService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MovementsList extends Component
{
    public array $incomes = [];

    private MovementsService $service;

    public function boot()
    {
        $this->service = MovementsService::create();
    }


    public function render(): View
    {
        if (!$this->service->hasMembers()) {
            return view('livewire.home.movements-list-empty');
        }

        if (!$this->service->hasBills()) {
            return view('livewire.home.movements-list-empty');
        }

        $this->service->setIncomes($this->incomes);
        $movements = $this->service->toMovements();

        if ($movements->count() === 0) {
            return view('livewire.home.movements-list-empty');
        }

        return view('livewire.home.movements-list', compact('movements'));
    }
}
