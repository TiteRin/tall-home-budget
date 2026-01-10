<?php

namespace Tests\Support;

use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Collection;

final class ImmutableTestFactory
{
    public function __construct(
        protected BillFactory $factory,
        protected ?Household  $household = null,
        protected ?Member     $member = null,
        protected ?User       $user = null,
        protected Collection  $members = new Collection(),
        protected Collection  $bills = new Collection(),
    )
    {
    }

    /* =====================
     * Withers (immutables)
     * ===================== */

    public function withHousehold(array $overrides = []): self
    {
        return new self(
            factory: $this->factory,
            household: $this->factory->household($overrides),
            member: $this->member,
            user: $this->user,
            members: $this->members,
            bills: $this->bills,
        );
    }

    public function withMember(array $overrides = []): self
    {
        $household = $this->household
            ?? $this->factory->household();

        $member = $this->factory->member($overrides, $household);

        return new self(
            factory: $this->factory,
            household: $household,
            member: $member,
            user: $this->user,
            members: $this->members->push($member),
            bills: $this->bills,
        );
    }

    public function withUser(array $overrides = []): self
    {
        $member = $this->member
            ?? $this->withMember()->member();

        $user = User::factory()->create(
            array_merge(['member_id' => $member->id], $overrides)
        );

        return new self(
            factory: $this->factory,
            household: $this->household,
            member: $member,
            user: $user,
            members: $this->members,
            bills: $this->bills,
        );
    }

    public function withBill(array $overrides = []): self
    {
        $member = $this->member
            ?? $this->withMember()->member();

        $bill = $this->factory->bill($overrides, $member);

        return new self(
            factory: $this->factory,
            household: $this->household,
            member: $member,
            user: $this->user,
            members: $this->members,
            bills: $this->bills->push($bill),
        );
    }

    /* =====================
     * Getters
     * ===================== */

    public function household(): ?Household
    {
        return $this->household;
    }

    public function member(): ?Member
    {
        return $this->member;
    }

    public function user(): User
    {
        return $this->user
            ?? $this->withUser()->user();
    }

    public function members(): Collection
    {
        return $this->members;
    }

    public function bills(): Collection
    {
        return $this->bills;
    }
}
