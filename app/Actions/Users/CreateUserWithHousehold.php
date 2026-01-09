<?php

namespace App\Actions\Users;

use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateUserWithHousehold
{
    /**
     * @throws ValidationException|\Throwable
     */
    public function execute(array $data): User
    {
        $this->validate($data);

        return DB::transaction(function () use ($data) {

            if (isset($data['member_id'])) {
                $member = Member::findOrFail($data['member_id']);
            } else {
                $household = Household::create([
                    'name' => $data['household_name'],
                    'default_distribution_method' => $data['default_distribution_method'],
                    'has_joint_account' => $data['has_joint_account']
                ]);

                $member = Member::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'household_id' => $household->id,
                ]);
            }

            $user = User::create([
                'email' => $data['email'],
                'password' => $data['password'],
                'member_id' => $member->id,
            ]);

            return $user;
        });
    }

    /**
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    protected function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'household_name' => ['required', 'string', 'min:3', 'max:255'],
            'default_distribution_method' => ['required', 'string'],
            'has_joint_account' => ['required', 'boolean'],
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

}
