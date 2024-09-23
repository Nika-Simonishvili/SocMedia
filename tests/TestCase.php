<?php

namespace Tests;

use Database\Seeders\AclSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(AclSeeder::class);
    }

    public static function getDecodedContent(TestResponse $response): array
    {
        return json_decode($response->content(), true);
    }
}
