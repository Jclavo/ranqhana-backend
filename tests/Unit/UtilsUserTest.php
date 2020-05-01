<?php

namespace Tests\Unit;

use App\User;
use App\Utils\UserUtils;

use Tests\TestCase;

class UtilsUserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_util_user_get_timezone()
    {
        $user = factory(User::class)->create(['store_id' => 1]);

        $timezone = UserUtils::getTimezone($user);

        $this->assertEquals($timezone,'America/Sao_Paulo');
    }
}
