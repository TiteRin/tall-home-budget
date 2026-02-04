<?php

use App\Models\Household;
use App\Models\Member;
use App\Models\User;

beforeEach(function () {
    $this->household = Household::factory()->create();
    $this->member = Member::factory()->create(['household_id' => $this->household->id]);
});

describe('HomeController', function () {
    it('shows welcome page for guests', function () {
        $this->get(route('home'))
            ->assertStatus(200)
            ->assertViewIs('welcome');
    });

    it('redirects to household settings if user has no household', function () {
        $user = User::factory()->create(['member_id' => null]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('household.settings'));
    });

    it('shows home page if user has a household', function () {
        $user = User::factory()->create(['member_id' => $this->member->id]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertStatus(200)
            ->assertViewIs('home')
            ->assertViewHas('household');
    });
});
