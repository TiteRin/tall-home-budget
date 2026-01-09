<?php

namespace Tests\Feature\Livewire\Auth;

use App\Enums\DistributionMethod;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

describe('Register component', function () {

    test('register page is accessible', function () {
        $response = $this->get('/register');
        $response->assertStatus(200);
    });

    test('a user can register with household', function () {
        Event::fake();

        Livewire::test(Register::class)
            ->set('firstName', 'John')
            ->set('lastName', 'Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123!')
            ->set('passwordConfirmation', 'password123!')
            ->set('householdName', 'Doe Family')
            ->set('defaultDistributionMethod', DistributionMethod::EQUAL->value)
            ->set('hasJointAccount', false)
            ->call('register')
            ->assertRedirect(route('login'));

        $this->assertTrue(User::whereEmail('john@example.com')->exists());

        $user = User::whereEmail('john@example.com')->first();
        expect($user->member->first_name)->toBe('John')
            ->and($user->member->last_name)->toBe('Doe')
            ->and($user->member->household->name)->toBe('Doe Family');

        Event::assertDispatched(Registered::class);
    });

    describe('Validation', function () {

        beforeEach(function () {
            $this->component = Livewire::test(Register::class)
                ->set('firstName', 'John')
                ->set('lastName', 'Doe')
                ->set('email', 'john@example.com')
                ->set('password', 'password123!')
                ->set('passwordConfirmation', 'password123!')
                ->set('householdName', 'Doe Family')
                ->set('defaultDistributionMethod', DistributionMethod::EQUAL->value)
                ->set('hasJointAccount', false);
        });

        test('first name is required', function () {
            $this->component->set('firstName', '')
                ->call('register')
                ->assertHasErrors(['firstName' => 'required']);
        });

        test('last name is required', function () {
            $this->component->set('lastName', '')
                ->call('register')
                ->assertHasErrors(['lastName' => 'required']);
        });

        test('email is required', function () {
            $this->component->set('email', '')
                ->call('register')
                ->assertHasErrors(['email' => 'required']);
        });

        test('email must be a valid email address', function () {
            $this->component->set('email', 'invalid-email')
                ->call('register')
                ->assertHasErrors(['email' => 'email']);
        });

        test('email must be unique', function () {
            User::factory()->create(['email' => 'duplicate@example.com']);
            $this->component->set('email', 'duplicate@example.com')
                ->call('register')
                ->assertHasErrors(['email' => 'unique']);
        });

        test('password must be confirmed', function () {
            $this->component->set('password', 'password123!')
                ->set('passwordConfirmation', 'different')
                ->call('register')
                ->assertHasErrors(['password']);
        });

        test('password should be at least 8 characters', function () {
            $this->component->set('password', 'abc')
                ->call('register')
                ->assertHasErrors(['password' => 'min']);
        });

        test('household name is required', function () {
            $this->component->set('householdName', '')
                ->call('register')
                ->assertHasErrors(['householdName' => 'required']);
        });

        test('default distribution method is required', function () {
            $this->component->set('defaultDistributionMethod', '')
                ->call('register')
                ->assertHasErrors(['defaultDistributionMethod' => 'required']);
        });

        test('user is not authenticated after registration', function () {
            $this->component->call('register');

            $this->assertGuest();
        });
    });

    describe('Invite registration', function () {
        test('it pre-fills fields when member_id is present in URL', function () {
            $member = \App\Models\Member::factory()->create([
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'household_id' => \App\Models\Household::factory()->create([
                    'name' => 'Smith Household',
                    'has_joint_account' => true,
                    'default_distribution_method' => DistributionMethod::PRORATA,
                ])->id
            ]);

            Livewire::withQueryParams(['member_id' => $member->id])
                ->test(Register::class)
                ->assertSet('memberId', $member->id)
                ->assertSet('firstName', 'Jane')
                ->assertSet('lastName', 'Smith')
                ->assertSet('householdName', 'Smith Household')
                ->assertSet('hasJointAccount', true)
                ->assertSet('defaultDistributionMethod', DistributionMethod::PRORATA->value);
        });

        test('it registers a user linked to the existing member', function () {
            Event::fake();

            $member = \App\Models\Member::factory()->create([
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'household_id' => \App\Models\Household::factory()->create(['name' => 'Smith Household'])->id
            ]);

            Livewire::withQueryParams(['member_id' => $member->id])
                ->test(Register::class)
                ->set('email', 'jane@example.com')
                ->set('password', 'password123!')
                ->set('passwordConfirmation', 'password123!')
                ->call('register')
                ->assertRedirect(route('login'));

            $user = User::whereEmail('jane@example.com')->first();
            expect($user->member_id)->toBe($member->id)
                ->and($user->member->first_name)->toBe('Jane')
                ->and($user->member->household->name)->toBe('Smith Household');

            // Vérifier qu'on n'a pas créé un nouveau membre mais utilisé l'existant
            expect(\App\Models\Member::where('first_name', 'Jane')->count())->toBe(1);

            Event::assertDispatched(Registered::class);
        });

        test('it fails if member is already linked to a user', function () {
            $user = User::factory()->create();
            $member = $user->member;

            $this->get(route('register', ['member_id' => $member->id]))
                ->assertStatus(403);
        });
    });
});
