<?php

namespace App\Livewire\Home;

use App\Models\Household;
use Livewire\Component;

class GeneralInformation extends Component
{

    #[Prop]
    public Household $household;

    public function render()
    {
        return view('livewire.home.general-information');
    }
}
