<?php

namespace Tests\Unit;

use App\User;
use App\Invoice;
use App\Actions\User\UserIsFree;
use Tests\TestCase;

class ActionUserIsFreeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_action_user_is_free_fail()
    {
        $user = factory(User::class)->create();
        $invoice = factory(Invoice::class)->create(['user_id' => $user->id]);
        $action = new UserIsFree($user);

        $this->assertFalse($action->passes());

    }

    public function test_action_user_is_free_ok()
    {
        $user = factory(User::class)->create();
        $action = new UserIsFree($user);

        $this->assertTrue($action->passes());

    }
}
