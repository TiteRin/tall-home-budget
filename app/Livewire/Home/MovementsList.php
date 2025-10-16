<?php

namespace App\Livewire\Home;

use App\Services\Movement\MovementsService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MovementsList extends Component
{
    public array $incomes = [];

    private MovementsService $service;

    public function boot()
    {
        $this->service = MovementsService::create();
    }

    #[Computed]
    public function movements()
    {
        return $this->service->toMovements();
    }

    public function updatedIncomes(): void
    {
        $this->service->setIncomes($this->incomes);
    }


    public function render(): View
    {
        if (!$this->service->hasMembers()) {
            return view('livewire.home.movements-list-empty');
        }

        if (!$this->service->hasBills()) {
            return view('livewire.home.movements-list-empty');
        }

        if ($this->movements->count() === 0) {
            return view('livewire.home.movements-list-empty');
        }

        return view('livewire.home.movements-list');
    }
}
