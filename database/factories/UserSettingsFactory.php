<?php

namespace Database\Factories;

use App\Enums\ChannelsEnum;
use App\Models\UserSettings;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSettingsFactory extends Factory
{
    protected $model = UserSettings::class;

    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'notification_channels' => [
                ChannelsEnum::DATABASE,
                ChannelsEnum::MAIL,
            ],
        ];
    }
}
