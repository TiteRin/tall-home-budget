<?php

namespace App\Livewire\Auth;

use App\Actions\Users\CreateUserWithHousehold;
use App\Enums\DistributionMethod;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Log;
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

    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'min:2', 'max:255'],
            'lastName' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required', 'string'],
            'householdName' => ['required', 'string', 'min:2', 'max:255'],
            'defaultDistributionMethod' => ['required', Rule::enum(DistributionMethod::class)],
            'hasJointAccount' => ['required', 'boolean'],
        ];
    }

    public function register(CreateUserWithHousehold $action)
    {
        $this->validate();

        try {
            $user = $action->execute([
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'password' => $this->password,
                'household_name' => $this->householdName,
                'default_distribution_method' => $this->defaultDistributionMethod,
                'has_joint_account' => $this->hasJointAccount
            ]);

            event(new Registered($user));

            session()->flash('success', 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.');

            $this->redirect(route('login'));
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $errors) {
                $this->addError($field, $errors[0]);
            }
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
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
