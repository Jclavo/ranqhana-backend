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
        $this->setFieldsDatabaseHas(['id', 'name', 'identification', 'email', 'store_id', 'password']);  
    }

    //TEST FUNCTION create user
    public function test_user_create_name_required()
    {       
        $this->user_name_required('C');        
    }

    public function test_user_create_name_too_long()
    {    
        $this->user_name_too_long('C');
    }

    public function test_user_create_identification_required()
    {   
        $this->user_identification_required('C'); 
    }

    public function test_user_create_identification_only_digits()
    {    
        $this->user_identification_only_digits('C');
    }

    public function test_user_create_identification_taken()
    {       
       $this->user_identification_taken('C');
    }

    public function test_user_create_identification_lenght_for_Brazil()
    {       
        $this->user_identification_lenght_for_Brazil('C');
    }

    public function test_user_create_email_format()
    {       
        $this->user_email_format('C');
    }

    public function test_user_create_email_taken()
    {       
        $this->user_email_taken('C');
    }

    public function test_user_create_store_id_required()
    {   
        $this->user_store_id_required('C');
    }

    public function test_user_create_store_id_exist()
    {       
        $this->user_store_id_exist('C');
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

    //update function
    public function test_user_update_name_required()
    {       
        $this->user_name_required('U');   
    }

    public function test_user_update_name_too_long()
    {    
        $this->user_name_too_long('U');
    }

    public function test_user_update_identification_required()
    {   
        $this->user_identification_required('U'); 
    }

    public function test_user_update_identification_only_digits()
    {    
        $this->user_identification_only_digits('U');
    }

    public function test_user_update_identification_taken()
    {       
       $this->user_identification_taken('U');
    }

    // public function test_user_update_identification_lenght_for_Brazil()
    // {       
    //     $this->user_identification_lenght_for_Brazil('U');
    // }

    public function test_user_update_email_format()
    {       
        $this->user_email_format('U');
    }

    public function test_user_update_email_taken()
    {       
        $this->user_email_taken('U');
    }

    public function test_user_update_store_id_required()
    {   
        $this->user_store_id_required('U');
    }

    public function test_user_update_store_id_exist()
    {       
        $this->user_store_id_exist('U');
    }

    public function test_user_update_password_length_min(){
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['password' => $this->faker->regexify('[A-Za-z0-9]{7}')]);

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                            ->put('api/users/' . $user->id , $user->toArray()); 
        
        // Verify status 200 
        $response->assertStatus(200);
                        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The password must be at least 8 characters.']);
    }

    public function test_user_update_password_length_max(){
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['password' => $this->faker->regexify('[A-Za-z0-9]{50}')]);

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                            ->put('api/users/' . $user->id , $user->toArray()); 
        
        // Verify status 200 
        $response->assertStatus(200);
                        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The password may not be greater than 45 characters.']);
    }

    public function test_user_update_password_match(){
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['password' => $this->faker->regexify('[A-Za-z0-9]{10}')]);
        $user->repassword = $this->faker->regexify('[A-Za-z0-9]{10}');
        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                            ->put('api/users/' . $user->id , $user->toArray()); 
        
        // Verify status 200 
        $response->assertStatus(200);
                        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The repassword and password must match.']);
    }

    public function test_user_update_ok_without_password_for_Brazil()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(true);

        // Generate a user 
        $store = factory(Store::class)->create(['country_id' => 1]);
        $user = factory(User::class)->create();
        $userUpdate = factory(User::class)->make(['identification' => $this->faker->regexify('[0-9]{11}'),
                                                'store_id' => $store->id,
                                                'password' => '' ]);
        //Assign email
        $user->name = $userUpdate->name;
        $user->identification = $userUpdate->identification;
        $user->email = $userUpdate->email;
        $user->store_id = $userUpdate->store_id;

        $oldPassword = $user->password;
        $user->password = '';
        // unset($user->password);
        // $user->repassword = null;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->put('api/users/' . $user->id , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);

        //Verify in the database
        $this->setResultResponse($response);
        $this->checkRecordInDB();

        //Check password was not updated
        $this->assertEquals(json_decode($response->content(),true)['result']['password'],$oldPassword);
                     
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'User updated successfully.']);
    }

    public function test_user_update_ok_for_Brazil()
    {       
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(true);

        // Generate a user 
        $store = factory(Store::class)->create(['country_id' => 1]);
        $user = factory(User::class)->create();
        $userUpdate = factory(User::class)->make(['identification' => $this->faker->regexify('[0-9]{11}'),
                                                'store_id' => $store->id,
                                                'password' => $this->faker->regexify('[A-Za-z0-9]{10}')]);
        //Assign email
        $user->name = $userUpdate->name;
        $user->identification = $userUpdate->identification;
        $user->email = $userUpdate->email;
        $user->store_id = $userUpdate->store_id;
        $user->password = $userUpdate->password;
        $user->repassword = $userUpdate->password;

        // Call method
        $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                          ->put('api/users/' . $user->id , $user->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);

        //Verify in the database
        $this->setResultResponse($response);
        $this->checkRecordInDB();
                     
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'User updated successfully.']);
    }

    //Share functions to create/update
    private function user_name_required($option = ''){
        $this->checkOptionCRUD($option);      

        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['name' => '']);

        // Call method
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U': 
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray());
                break;
        }
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name field is required.']);
    }

    private function user_name_too_long($option = ''){
        $this->checkOptionCRUD($option);      
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['name' => $this->faker->regexify('[A-Za-z0-9]{50}')]);

        // Call method
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                    ->post('api/users/' , $user->toArray());
                break;
            case 'U': 
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/'.$user->id , $user->toArray());
                break;
        }
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name may not be greater than 45 characters.']);
    }

    private function user_identification_required($option = ''){
        $this->checkOptionCRUD($option);
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create(['identification' => '']);

        // Call method
        switch ($option) {
        case 'C':
            $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                              ->post('api/users/' , $user->toArray());
            break;
        case 'U':
            $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                              ->put('api/users/' . $user->id , $user->toArray()); 
            break;
        }
    
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification field is required.']);
    }

    private function user_identification_only_digits($option = ''){
        $this->checkOptionCRUD($option);
        //Authentication
        $this->get_api_token();

        // db config
        $this->setDatabaseHas(false);

        // Generate a user 
        $user = factory(User::class)->create();
        $userNew = factory(User::class)->make(['identification' => $this->faker->lexify('????')]);
        $user->identification = $userNew->identification;

        // Call method
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray()); 
                break;
            }

        // Verify status 200 
        $response->assertStatus(200);
                    
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification must be a number.']);

    }

    private function user_identification_taken($option = ''){
        $this->checkOptionCRUD($option);
         //Authentication
         $this->get_api_token();

         // db config
         $this->setDatabaseHas(false);
 
         // Generate a user 
         $user = factory(User::class)->create();
 
        // Call method
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U':
                $userUpdate = factory(User::class)->create();
                $user->identification = $userUpdate->identification;
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray()); 
                break;
            }
         
         // Verify status 200 
         $response->assertStatus(200);
                      
         // Verify values in response
         $response->assertJson(['status' => false]);
         $response->assertJson(['message' => 'The identification has already been taken.']);
    }

    private function user_identification_lenght_for_Brazil($option = ''){
        $this->checkOptionCRUD($option);
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
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray()); 
                break;
            }
        
        // Verify status 200 
        $response->assertStatus(200);
                        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification must be 11 characters for your store country.']);

    }

    private function user_email_format($option = ''){
        $this->checkOptionCRUD($option);
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
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray()); 
                break;
            }
        
        // Verify status 200 
        $response->assertStatus(200);
                     
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The email must be a valid email address.']);

    }

    private function user_email_taken($option = ''){
        $this->checkOptionCRUD($option);

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
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U':
                $userUpdate = factory(User::class)->create();
                $user->email = $userUpdate->email;
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray()); 
                break;
            }
        
        // Verify status 200 
        $response->assertStatus(200);
                        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The email has already been taken.']);
    }

    private function user_store_id_required($option = ''){
        $this->checkOptionCRUD($option);

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
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U':
                $userUpdate = factory(User::class)->create();
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray()); 
                break;
            }
        
        // Verify status 200 
        $response->assertStatus(200);
                        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The store id field is required.']);

    }

    private function user_store_id_exist($option = ''){
        $this->checkOptionCRUD($option);
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
        switch ($option) {
            case 'C':
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->post('api/users/' , $user->toArray());
                break;
            case 'U':
                $userUpdate = factory(User::class)->create();
                $response =  $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                                  ->put('api/users/' . $user->id , $user->toArray()); 
                break;
            }
        
        // Verify status 200 
        $response->assertStatus(200);
                        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The selected store id is invalid.']);

    }


    
}