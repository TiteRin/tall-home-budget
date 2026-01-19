<?php

namespace Tests\Feature\Livewire;

use App\Livewire\UserProfile;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $member = Member::factory()->create();
        $this->user = User::factory()->create([
            'member_id' => $member->id,
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_update_email()
    {
        Livewire::test(UserProfile::class)
            ->set('email', 'new@example.com')
            ->call('updateEmail')
            ->assertHasNoErrors()
            ->assertSee('Adresse e-mail mise à jour avec succès.');

        $this->user->refresh();
        $this->assertEquals('new@example.com', $this->user->email);
    }

    /** @test */
    public function it_validates_email_is_unique()
    {
        User::factory()->create(['email' => 'other@example.com']);

        Livewire::test(UserProfile::class)
            ->set('email', 'other@example.com')
            ->call('updateEmail')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function it_can_update_password()
    {
        Livewire::test(UserProfile::class)
            ->set('current_password', 'password123')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertSee('Mot de passe mis à jour avec succès.');

        $this->user->refresh();
        $this->assertTrue(Hash::check('new-password', $this->user->password));
    }

    /** @test */
    public function it_requires_current_password_to_update_password()
    {
        Livewire::test(UserProfile::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);
    }

    /** @test */
    public function it_validates_password_confirmation()
    {
        Livewire::test(UserProfile::class)
            ->set('current_password', 'password123')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'different-password')
            ->call('updatePassword')
            ->assertHasErrors(['password' => 'confirmed']);
    }
}
