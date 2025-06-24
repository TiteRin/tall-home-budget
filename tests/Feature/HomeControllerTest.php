<?php

namespace App\Tests\Feature;

use App\Models\Household;
use App\Enums\DistributionMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_redirects_to_household_settings_when_no_household_exists()
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('household.settings'));
    }

    /** @test */
    public function it_shows_home_page_when_household_exists()
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $response = $this->get(route('home'));

        $response->assertSuccessful();
        $response->assertViewIs('home');
        $response->assertViewHas('household', $household);
        $response->assertSee('Foyer Test Household');
        $response->assertSee('50/50'); // Label pour EQUAL
        $response->assertSee('Non'); // Compte joint
    }
} 