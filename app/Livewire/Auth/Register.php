<?php

namespace App\Livewire\Auth;

use App\Actions\Users\CreateUserWithHousehold;
use App\Enums\DistributionMethod;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Nette\Schema\ValidationException;

class Register extends Component
{
    /** @var string */
    public $firstName = '';

    /** @var string */
    public $lastName = '';

    /** @var string */
    public $email = '';

    /** @var string */
    public $password = '';

    /** @var string */
    public $passwordConfirmation = '';

    /**
     * @var string
     */
    public $householdName = '';

    /** @var string */
    public $defaultDistributionMethod = DistributionMethod::PRORATA->value;

    /** @var bool */
    public $hasJointAccount = false;

    public function register(CreateUserWithHousehold $action)
    {
        try {
            $user = $action->execute([
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'household_name' => $this->householdName,
                'default_distribution_method' => $this->defaultDistributionMethod,
                'has_joint_account' => $this->hasJointAccount
            ]);

            event(new Registered($user));

            $this->redirect(route('login'));
        } catch (ValidationException $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register', [
            'distributionMethods' => DistributionMethod::cases(),
        ])->extends('layouts.app');
    }
}
