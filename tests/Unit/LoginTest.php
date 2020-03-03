<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\User;
use App\Country;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    //use DatabaseMigrations;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed database
        //$this->seed();
       
    }

    public function test_register_country_code_is_required()
    {
        // Generate an user object
        $user = factory(User::class)->make(['country_code' => '']);
              
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
        
        
        //Verify in the database
        $this->assertDatabaseMissing('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The country code field is required.']);
    }
 
    public function test_register_email_format_is_invalid()
    {
        // Generate an user object
        $user = factory(User::class)->make(['email' => 'myemail.com', 'country_code' => '55' ]);
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The email must be a valid email address.']);
    }

    public function test_register_password_is_required()
    {
        // Generate an user object
        $user = factory(User::class)->make(['password' => '']);
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The password field is required.']);
    }


    public function test_register_repassword_is_required()
    {
        // Generate an user object
        $user = factory(User::class)->make();
              
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
        
        
        //Verify in the database
        $this->assertDatabaseMissing('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The c password field is required.']);
    }
       
     
    public function test_register_password_repassword_are_not_equal()
    {
        // Generate an user object
        $user = factory(User::class)->make(['password' => 'secret']);
        
        $user->c_password = 'not_secret';

        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
               
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The c password and password must match.']);
    }

    public function test_register_store_id_is_required()
    {
        // Generate an user object
        $user = factory(User::class)->make(['store_id' => '']);
              
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
        
        
        //Verify in the database
        $this->assertDatabaseMissing('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The c password field is required.']);
    }

    public function test_register_identification_from_brazil_is_required()
    {

        // Generate an user object
        $user = factory(User::class)->make(['identification' => '', 'country_code' => '55']);
        // set c_password field
        $user->c_password = $user->password;
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
               
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification field is required.']);
    }

    

    public function test_register_identification_from_brazil_length_is_11()
    {

        // Generate an user object
        $user = factory(User::class)->make(['identification' => '123', 'country_code' => '55']);
        // set c_password field
        $user->c_password = $user->password;
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
               
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification must be 11 digits.']);
    }

    public function test_register_identification_from_brazil_only_accepts_digits()
    {

        // Generate an user object
        $user = factory(User::class)->make(['identification' => '123123123as', 'country_code' => '55']);
        // set c_password field
        $user->c_password = $user->password;
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
               
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        //$response->assertJson(['message' => 'The identification must be a number.']);
        $response->assertJson(['message' => 'The identification must be 11 digits.']);
    }
  
    public function test_register_an_user()
    {
        // Generate an user object
        $user = factory(User::class)->make(['country_code' => '55']);
        
        $userMade = $user;
        $user->c_password = $user->password;
        //Submit post request to create an user endpoint
        $response = $this->post('api/register', $user->toArray());
        
        //Verify in the database
        //$this->assertDatabaseHas('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'User created successfully.']);
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
          ]);
    }
    

     //FUNCTION LOGIN



     public function test_login_country_code_is_required()
    {
        // Generate an user object
        $user = factory(User::class)->create();
              
        // set values to login
        $user->country_code = '';

        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The country code field is required.']);
    }
 
    public function test_login_password_is_required()
    {
        // Generate an user object
        $user = factory(User::class)->create();
        
         // set values to login
         $user->password = '';

        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('users', $user->toArray());
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The password field is required.']);
    }

    public function test_login_identification_from_brazil_is_required()
    {

        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55']);
        
        // set c_password field
        $user->identification = '';
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
               
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification field is required.']);
    }

    

    public function test_login_identification_from_brazil_length_is_11()
    {

        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55']);
        
        // set c_password field
        $user->identification = '123';
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
               
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The identification must be 11 digits.']);
    }

    public function test_login_identification_from_brazil_only_accepts_digits()
    {

        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55']);
        
        // set c_password field
        $user->identification = '123123123as';
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
               
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        //$response->assertJson(['message' => 'The identification must be a number.']);
        $response->assertJson(['message' => 'The identification must be 11 digits.']);
    }

    public function test_login_user_correctly()
    {
        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55', 'email' => '']);
        
        $this->assertDatabaseHas('users', $user->toArray());
        
        $user->password = 'secret';

        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
        
        //Verify in the database
        
        // Verify status 200
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
          ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'User logged successfully.']);
         
    }
    
    
    public function test_login_response_api_token()
    {
        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55', 'email' => '']);
        
        $this->assertDatabaseHas('users', $user->toArray());
        
        $user->password = 'secret';
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
        
        //Verify in the database
             
        $this->assertNotEmpty(json_decode($response->content(),true)['result']['api_token']);
        
    }
    
    public function test_unauthenticated_user()
    {
        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55', 'email' => '']);
       
        $user->password = 'secret';
        
        //Submit post request to create an user endpoint
        $this->post('api/login', $user->toArray());
        
        //Verify in the database
        
        $response = $this->get('api/getUserInformation');
              
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Unauthenticated.']);
        
    }
    
    
    public function test_only_user_logged()
    {
        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55', 'email' => '']);
        
        $user->password = 'secret';
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
        
        //Verify in the database
        $this->assertNotEmpty(json_decode($response->content(),true)['result']['api_token']);
        
        /*$response = $this->get('api/getUserInformation', [], [], [
            'headers' => [
                'HTTP_AUTHORIZATION' => 'bearer ' . json_decode($response->content(),true)['result']['api_token']
                //'CONTENT_TYPE' => 'application/ld+json',
                //'HTTP_ACCEPT' => 'application/ld+json'
            ]]);*/
        
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. json_decode($response->content(),true)['result']['api_token']])
              ->get('api/getUserInformation');
        
        
        $response->assertJsonStructure([
        'status',
        'message',
        'result' => []
        ]);      
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'User information gotten successfully.']);
        //$response->assertJson(['result' => $user->toArray()]);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    

}
