<?php
namespace Tests\Unit\Models;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseholdTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_household(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $this->assertDatabaseHas('households', $household->toArray());
    }

    public function test_can_update_household(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->update([
            'name' => 'Updated Household',
            'has_joint_account' => true,
            'default_distribution_method' => DistributionMethod::PRORATA,
        ]);

        $this->assertDatabaseHas('households', [
            'id' => $household->id,
            'name' => 'Updated Household',
            'has_joint_account' => true,
            'default_distribution_method' => DistributionMethod::PRORATA->value,
        ]);
    }

    public function test_can_delete_household(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->delete();

        $this->assertDatabaseMissing('households', $household->toArray());
    }

    public function test_can_add_household_member(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->members()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertDatabaseHas('members', [
            'household_id' => $household->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    public function test_can_remove_household_member(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->members()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $household->members()->where('first_name', 'John')->delete();

        $this->assertDatabaseMissing('members', [
            'household_id' => $household->id,
            'first_name' => 'John',
        ]);
    }

    public function test_can_get_household_members(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->members()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $members = $household->members;

        $this->assertCount(1, $members);
        $this->assertEquals('John', $members->first()->first_name);
        $this->assertEquals('Doe', $members->first()->last_name);
    }

    public function test_can_get_household_default_distribution_method(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
            'default_distribution_method' => DistributionMethod::EQUAL,
        ]);

        $this->assertEquals(DistributionMethod::EQUAL, $household->default_distribution_method);
    }

    public function test_household_has_equal_distribution_method_by_default(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
        ]);

        $this->assertEquals(DistributionMethod::EQUAL, $household->getDefaultDistributionMethod());
    }

    public function test_household_has_total_amount_zero_by_default(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
        ]);

        $this->assertEquals(new Amount(0), $household->total_amount);
    }

    public function test_household_has_total_amount_after_adding_bill(): void
    {
        $household = Household::create([
            'name' => 'Test Household',
            'has_joint_account' => false,
        ]);

        $member = Member::create([
            'household_id' => $household->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $household->bills()->create([
            'name' => 'Test Bill',
            'amount' => 10000,
            'member_id' => $member->id,
            'household_id' => $household->id,
            'distribution_method' => DistributionMethod::EQUAL,
        ]);

        $household->bills()->create([
            'name' => 'Test Bill 2',
            'amount' => 20000,
            'member_id' => $member->id,
            'household_id' => $household->id,
            'distribution_method' => DistributionMethod::EQUAL,
        ]);

        $this->assertEquals(new Amount(30000), $household->total_amount);
    }
}
