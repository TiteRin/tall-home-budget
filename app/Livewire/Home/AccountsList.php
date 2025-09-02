<?php

namespace App\Livewire\Home;

use Exception;
use Illuminate\View\View;
use Livewire\Component;

class AccountsList extends Component
{

    public function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public function mount()
    {
        throw new Exception("No household exists");
    }

    public function render(): View
    {
        return view('livewire.home.accounts-list');
    }
}
