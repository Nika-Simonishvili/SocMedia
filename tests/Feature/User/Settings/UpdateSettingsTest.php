<?php

namespace Tests\Feature\User\Settings;

use App\Enums\ChannelsEnum;
use App\Models\UserSettings;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateSettingsTest extends TestCase
{
    #[Test]
    public function it_throws_unauthorized(): void
    {
        $response = $this->putJson('api/user/settings', ['notification_channels' => ChannelsEnum::DATABASE]);

        $response->assertUnauthorized();
    }

    #[Test]
    #[DataProvider('invalidData')]
    public function it_should_throw_validation_errors($data): void
    {
        $user = $this->login();

        $response = $this->putJson('api/user/settings', $data);

        $response->assertUnprocessable();
        $this->assertDatabaseEmpty((new UserSettings)->getTable());
    }

    public static function invalidData(): array
    {
        return [
            'empty-data' => [
                'data' => [],
            ],
            'invalid-channel' => [
                'data' => [
                    'notification_channels' => fake()->word(),
                ],
            ],
        ];
    }

    #[Test]
    public function it_should_update_user_notification_channels(): void
    {
        $user = $this->login();
        $settings = UserSettings::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas((new UserSettings)->getTable(),
            [
                'user_id' => $user->id,
                'notification_channels' => $this->castAsJson([ChannelsEnum::DATABASE, ChannelsEnum::MAIL]),
            ]);

        $data = [
            'notification_channels' => [
                ChannelsEnum::DATABASE,
            ],
        ];

        $response = $this->putJson('api/user/settings', $data);

        $response->assertOk();
        $this->assertDatabaseHas((new UserSettings)->getTable(),
            [
                'notification_channels' => $this->castAsJson([ChannelsEnum::DATABASE]),
            ]
        );
    }
}
