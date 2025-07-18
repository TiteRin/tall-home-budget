<?php
namespace App\Tests\Feature;

use App\Models\Household;
use App\DistributionMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $this->assertDatabaseHas('household_members', [
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

        $this->assertDatabaseMissing('household_members', [
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
}
