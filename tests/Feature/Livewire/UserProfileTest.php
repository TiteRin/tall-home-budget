<?php

use App\Livewire\UserProfile;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

describe('UserProfile Component', function () {
    beforeEach(function () {
        $member = Member::factory()->create();
        $this->user = User::factory()->create([
            'member_id' => $member->id,
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($this->user);
    });

    test('it can update email', function () {
        Livewire::test(UserProfile::class)
            ->set('email', 'new@example.com')
            ->call('updateEmail')
            ->assertHasNoErrors()
            ->assertSee('Adresse e-mail mise à jour avec succès.');

        $this->user->refresh();
        expect($this->user->email)->toBe('new@example.com');
    });

    test('it validates email is unique', function () {
        User::factory()->create(['email' => 'other@example.com']);

        Livewire::test(UserProfile::class)
            ->set('email', 'other@example.com')
            ->call('updateEmail')
            ->assertHasErrors(['email' => 'unique']);
    });

    test('it can update password', function () {
        Livewire::test(UserProfile::class)
            ->set('current_password', 'password123')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSee('Mot de passe mis à jour avec succès.');

        $this->user->refresh();
        expect(Hash::check('new-password', $this->user->password))->toBeTrue();
    });

    test('it requires current_password to update password', function () {
        Livewire::test(UserProfile::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    });

    test('it validates password confirmation', function () {
        Livewire::test(UserProfile::class)
            ->set('current_password', 'password123')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors(['password' => 'confirmed']);
    });

    test('it requires password to delete account', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::actingAs($user)
            ->test(UserProfile::class)
            ->set('delete_confirm_password', 'wrong-password')
            ->call('deleteAccount')
            ->assertHasErrors(['delete_confirm_password']);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    });

    test('it deletes only user if other users exist in household', function () {
        $household = Household::factory()->create();
        $member1 = Member::factory()->create(['household_id' => $household->id]);
        $member2 = Member::factory()->create(['household_id' => $household->id]);

        $user1 = User::factory()->create([
            'member_id' => $member1->id,
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::factory()->create([
            'member_id' => $member2->id,
        ]);

        Livewire::actingAs($user1)
            ->test(UserProfile::class)
            ->set('delete_confirm_password', 'password123')
            ->call('deleteAccount')
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
        $this->assertDatabaseHas('members', ['id' => $member1->id]);
        $this->assertDatabaseHas('households', ['id' => $household->id]);
    });

    test('it deletes household if user is the only one in household', function () {
        $household = Household::factory()->create();
        $member = Member::factory()->create(['household_id' => $household->id]);
        $bill = Bill::factory()->create([
            'household_id' => $household->id,
            'member_id' => $member->id
        ]);

        $user = User::factory()->create([
            'member_id' => $member->id,
            'password' => Hash::make('password123'),
        ]);

        Livewire::actingAs($user)
            ->test(UserProfile::class)
            ->set('delete_confirm_password', 'password123')
            ->call('deleteAccount')
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
        $this->assertDatabaseMissing('bills', ['id' => $bill->id]);
        $this->assertDatabaseMissing('households', ['id' => $household->id]);
    });

    test('it deletes household if user is the only user even if other non-user members exist', function () {
        $household = Household::factory()->create();
        $member1 = Member::factory()->create(['household_id' => $household->id]); // L'utilisateur
        $member2 = Member::factory()->create(['household_id' => $household->id]); // Un autre membre sans compte

        $user = User::factory()->create([
            'member_id' => $member1->id,
            'password' => Hash::make('password123'),
        ]);

        Livewire::actingAs($user)
            ->test(UserProfile::class)
            ->set('delete_confirm_password', 'password123')
            ->call('deleteAccount')
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('members', ['id' => $member1->id]);
        $this->assertDatabaseMissing('members', ['id' => $member2->id]);
        $this->assertDatabaseMissing('households', ['id' => $household->id]);
    });
});
