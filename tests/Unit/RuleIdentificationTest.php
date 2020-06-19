<?php

namespace Tests\Unit;

use App\Models\User;

use App\Rules\Identification;
use Tests\TestCase;
use Faker;

class RuleIdentificationTest extends TestCase
{
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->faker = Faker\Factory::create();
    }

    public function test_rule_identification_empty()
    {
        $user = factory(User::class)->create();
        $action = new Identification();
        $this->assertFalse($action->passes('identification',$user->identification));
    }

    public function test_rule_identification_br_fail()
    {
        $user = factory(User::class,'brazilian')->create(['identification' => $this->faker->regexify('[A-Za-z0-9]{10}')]);
        $action = new Identification($user->store_id);
        $this->assertFalse($action->passes('identification',$user->identification));

    }

    public function test_rule_identification_br_ok()
    {
        $user = factory(User::class,'brazilian')->create();
        $action = new Identification($user->store_id);
        $this->assertTrue($action->passes('identification',$user->identification));
    }
    
}
