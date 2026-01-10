<?php

namespace Tests\Feature\Livewire\Onboarding;

use App\Livewire\Onboarding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected $household;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->household = bill_factory()->household();
        $this->member = bill_factory()->member(['first_name' => 'John'], $this->household);
        $this->user = User::factory()->create(['member_id' => $this->member->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_marks_household_configured_when_on_settings_page()
    {
        $this->get(route('household.settings'));

        Livewire::test(Onboarding::class)
            ->assertStatus(200);

        $this->household->refresh();
        $this->assertTrue($this->household->onboarding_configured_household);
    }

    /** @test */
    public function it_marks_bills_added_when_on_bills_page()
    {
        $this->get(route('bills'));

        Livewire::test(Onboarding::class)
            ->assertStatus(200);

        $this->household->refresh();
        $this->assertTrue($this->household->onboarding_added_bills);
    }

    /** @test */
    public function it_hides_when_both_are_completed()
    {
        $this->household->onboarding_configured_household = true;
        $this->household->onboarding_added_bills = true;
        $this->household->save();

        Livewire::test(Onboarding::class)
            ->assertDontSee('Pour bien commencer :');
    }

    /** @test */
    public function it_shows_checklist_when_not_completed()
    {
        $this->get(route('home'));

        Livewire::test(Onboarding::class)
            ->assertSee('Pour bien commencer :')
            ->assertSee('Paramétrer votre foyer')
            ->assertSee('Ajouter des dépenses');
    }
}
