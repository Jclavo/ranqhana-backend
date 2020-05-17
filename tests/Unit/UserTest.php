<?php

namespace Tests\Unit;

use App\User;
use App\Store;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->setBaseRoute('users');
        $this->setBaseModel('App\User');
        $this->setFaker(); 
        $this->setFieldsDatabaseHas(['id', 'name', 'identification', 'email', 'store_id']);  
    }

    //TEST FUNCTION create user
    public function test_user_create_name_required()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['name' => '']);

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name field is required.']);
        
    }

    public function test_user_create_name_too_long()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['name' => $this->faker->regexify('[A-Za-z0-9]{50}')]);

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name may not be greater than 45 characters.']);
    }

    public function test_user_create_identification_required()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['identification' => '']);

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification field is required.']);
    }

    public function test_user_create_identification_only_digits()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make(['identification' => $this->faker->lexify('????')]);
        $user->identification = $userNew->identification;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification must be a number.']);
    }

    public function test_user_create_identification_taken()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create();

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification has already been taken.']);
    }

    public function test_user_create_identification_lenght_for_Brazil()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $store = factory(Store::class)->create(['country_id' => 1]);
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make(['identification' => $this->faker->randomNumber(),
                                                'store_id' => $store->id]);
        //Assign email
        $user->name = $userNew->name;
        $user->identification = $userNew->identification;
        $user->email = $userNew->email;
        $user->store_id = $userNew->store_id;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification must be 11 characters for your store country.']);
    }

    public function test_user_create_email_format()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make(['email' => '']);
        //Assign email
        $user->identification = $userNew->identification;
        $user->email = $userNew->email;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The email must be a valid email address.']);
    }

    public function test_user_create_email_taken()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make();
        //Assign email
        $user->identification = $userNew->identification;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The email has already been taken.']);
    }

    public function test_user_create_store_id_required()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make(['store_id' => '']);
        //Assign email
        $user->identification = $userNew->identification;
        $user->email = $userNew->email;
        $user->store_id = $userNew->store_id;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The store id field is required.']);
    }

    public function test_user_create_store_id_exist()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make(['store_id' => '']);
        //Assign email
        $user->identification = $userNew->identification;
        $user->email = $userNew->email;
        $user->store_id = 0;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The selected store id is invalid.']);
    }

    public function test_user_create_ok_for_Brazil()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(true);

        // Generate a user 
        $store = factory(Store::class)->create(['country_id' => 1]);
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make(['identification' => $this->faker->regexify('[0-9]{11}'),
                                                'store_id' => $store->id]);
        //Assign email
        $user->name = $userNew->name;
        $user->identification = $userNew->identification;
        $user->email = $userNew->email;
        $user->store_id = $userNew->store_id;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->post('api/users/' , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);

        //Verify in the database
        $this->setResultResponse($response);
        $this->checkRecordInDB();
                     
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'User created successfully.']);
    }

    
}
