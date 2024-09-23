<?php

namespace Tests\Feature\Auth;

use App\Models\User;
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
            'invalid_email' => [
                'data' => [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'username' => fake()->lastName(),
                    'email' => 'dummy email',
                    'password' => fake()->password(),
                ],
            ],
            'already_existing_email' => [
                'data' => [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'username' => fake()->lastName(),
                    'email' => fake()->email(),
                    'password' => fake()->password(),
                ],
            ],
            'already_existing_username' => [
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
