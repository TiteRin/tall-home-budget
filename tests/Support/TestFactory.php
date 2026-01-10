<?php

namespace Tests\Support;

use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Collection;

class TestFactory
{

    protected ?Household $household = null;
    protected ?Member $member = null;
    protected ?User $user = null;
    protected Collection $members;
    protected Collection $users;

    public function __construct(
        protected BillFactory $billFactory
    )
    {
        $this->members = collect();
        $this->users = collect();
    }

    /**
     * Household
     */
    public function addHousehold(array $overrides = []): self
    {
        $this->household = $this->billFactory->household($overrides);
        return $this;
    }

    /**
     * Members
     */
    public function addMembers(int $count = 2, array $overrides = []): self
    {
        $this->household ??= $this->billFactory->household();

        $newMembers = $this->billFactory->members($count, $overrides, $this->household);
        $this->members = $this->members->merge($newMembers);
        return $this;
    }

    public function addMember(array $overrides = []): self
    {
        $this->addMembers(1, $overrides);
        $this->member = $this->members->last();
        return $this;
    }

    /**
     * Users
     */
    public function addUser(array $overrides = []): self
    {
        $this->member ??= $this->addMember($overrides);
        $this->user = User::factory()->create(
            array_merge(['member_id' => $this->member->id], $overrides)
        );

        $this->users->push($this->user);

        return $this;
    }

    /**
     * Getters
     */

    public function getHousehold(): ?Household
    {
        return $this->household;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function build(): array
    {
        return [
            'household' => $this->household,
            'members' => $this->members,
            'users' => $this->users,
            'member' => $this->getMember(),
            'user' => $this->getUser()
        ];
    }


}
