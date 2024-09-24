<?php

namespace App\Services;

use App\Enums\ChannelsEnum;
use App\Enums\RolesEnum;
use App\Models\User;

class UsersService
{
    public function setUpUserRelations(User $user): User
    {
        $user->assignRole(RolesEnum::USER);

        $user->settings()->create(
            [
                'notification_channels' => [ChannelsEnum::DATABASE],
            ],
        );

        return $user;
    }
}
