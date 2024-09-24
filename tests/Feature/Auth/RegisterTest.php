<?php

namespace Tests\Feature\Auth;

use App\Enums\ChannelsEnum;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\UserSettings;
use Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    #[Test]
    #[DataProvider('invalid_data')]
    public function it_should_throw_validation_errors($data): void
    {
        User::factory()->create(['email' => $data['email'], 'username' => $data['username']]);

        $response = $this->postJson('api/auth/register', $data);

        $response->assertStatus(422);
    }

    public static function invalid_data(): array
    {
        return [
            'invalid-email' => [
                'data' => [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'username' => fake()->lastName(),
                    'email' => 'dummy email',
                    'password' => fake()->password(),
                ],
            ],
            'already-existing-email' => [
                'data' => [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'username' => fake()->lastName(),
                    'email' => fake()->email(),
                    'password' => fake()->password(),
                ],
            ],
            'already-existing-username' => [
                'data' => [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'username' => fake()->lastName(),
                    'email' => fake()->email(),
                    'password' => fake()->password(),
                ],
            ],
        ];
    }

    #[Test]
    public function it_should_register_user(): void
    {
        Event::fake(Registered::class);

        $password = fake()->password();
        $data = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('api/auth/register', $data);

        $response->assertOk();
        $this->assertDatabaseHas((new User)->getTable(), Arr::except($data, ['password', 'password_confirmation']));

        /** @var User $user */
        $user = User::find(self::getDecodedContent($response)['data']['user']['id']);

        $this->assertTrue($user->hasRole(RolesEnum::USER));
        $this->assertDatabaseHas((new UserSettings)->getTable(),
            [
                'user_id' => $user->id,
                'notification_channels' => $this->castAsJson([ChannelsEnum::DATABASE->value]),
            ],
        );

        Event::assertDispatched(Registered::class);
    }

    #[Test]
    public function it_resends_email_verification(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('verification.send'));

        $response->assertOk();
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
