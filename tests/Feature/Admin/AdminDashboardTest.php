<?php

use App\Livewire\Admin\AdminDashboard;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Livewire\Livewire;
use function Pest\Laravel\get;
use function Pest\Laravel\withHeaders;

beforeEach(function () {
    config(['auth.admin.user' => 'admin']);
    config(['auth.admin.password_hash' => bcrypt('password')]);
});

test('unauthorized access is blocked', function () {
    get('/admin')
        ->assertStatus(401);
});

test('authorized access is allowed', function () {
    withHeaders([
        'PHP_AUTH_USER' => 'admin',
        'PHP_AUTH_PW' => 'password',
    ])->get('/admin')
        ->assertStatus(200);
});

test('it lists users with their information', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'created_at' => now()->subDays(10),
        'last_login_at' => now()->subDays(1),
    ]);

    Livewire::withHeaders([
        'PHP_AUTH_USER' => 'admin',
        'PHP_AUTH_PW' => 'password',
    ])->test(AdminDashboard::class)
        ->assertSee('user@example.com')
        ->assertSee($user->created_at->format('d/m/Y'))
        ->assertSee($user->last_login_at->format('d/m/Y'));
});

test('it can delete a user and its household if alone', function () {
    $household = Household::factory()->create();
    $member = Member::factory()->create(['household_id' => $household->id]);
    $user = User::factory()->create(['member_id' => $member->id]);

    Livewire::withHeaders([
        'PHP_AUTH_USER' => 'admin',
        'PHP_AUTH_PW' => 'password',
    ])->test(AdminDashboard::class)
        ->call('deleteUser', $user->id);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertDatabaseMissing('households', ['id' => $household->id]);
});

test('it deletes only user if other users exist in household', function () {
    $household = Household::factory()->create();
    $member1 = Member::factory()->create(['household_id' => $household->id]);
    $member2 = Member::factory()->create(['household_id' => $household->id]);

    $user1 = User::factory()->create([
        'member_id' => $member1->id,
    ]);

    $user2 = User::factory()->create([
        'member_id' => $member2->id,
    ]);

    Livewire::withHeaders([
        'PHP_AUTH_USER' => 'admin',
        'PHP_AUTH_PW' => 'password',
    ])->test(AdminDashboard::class)
        ->call('deleteUser', $user1->id);

    $this->assertDatabaseMissing('users', ['id' => $user1->id]);
    $this->assertDatabaseHas('users', ['id' => $user2->id]);
    $this->assertDatabaseHas('members', ['id' => $member1->id]);
    $this->assertDatabaseHas('households', ['id' => $household->id]);
});
