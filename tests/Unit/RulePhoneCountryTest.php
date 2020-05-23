<?php

namespace Tests\Unit;

use App\User;
use App\Rules\PhoneCountry;
use Tests\TestCase;
use Faker;

class RulePhoneCountryTest extends TestCase
{
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->faker = Faker\Factory::create();
    }

    public function test_rule_phone_empty()
    {
        $user = factory(User::class)->create();
        $action = new PhoneCountry();
        $this->assertFalse($action->passes('identification',$user->identification));
    }

    public function test_rule_phone_br_fail()
    {
        $user = factory(User::class,'brazilian')->create(['identification' => $this->faker->regexify('[A-Za-z0-9]{10}')]);
        $action = new PhoneCountry($user->store_id);
        $this->assertFalse($action->passes('identification',$user->identification));

    }

    public function test_rule_phone_br_ok()
    {
        $user = factory(User::class,'brazilian')->create();
        $action = new PhoneCountry($user->store_id);
        $this->assertTrue($action->passes('identification',$user->identification));
    }
}
