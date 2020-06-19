<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Country;
use App\Models\Store;

use App\Actions\User\UserBelongsToCountry;
use Tests\TestCase;

class ActionUserBelongsToCountryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_action_user_belongs_to_country_fail()
    {
        $country = factory(Country::class)->create();
        $user = factory(User::class)->create();
        $action = new UserBelongsToCountry($user->identification, $country->code);

        $this->assertFalse($action->passes());

    }

    public function test_action_user_belongs_to_country_ok()
    {
        $country = factory(Country::class)->create();
        $store = factory(Store::class)->create(['country_id' => $country->id ]);
        $user = factory(User::class)->create(['store_id' => $store->id]);

        $action = new UserBelongsToCountry($user->identification, $country->code);

        $this->assertTrue($action->passes());

    }
}
