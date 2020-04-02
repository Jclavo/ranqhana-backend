<?php

namespace Tests\Unit;

use App\User;
use App\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();      
    }

    public function get_api_token()
    {
        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55', 'email' => '']);
                
        $user->password = 'secret';
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());
        
        //Verify in the database
        $this->assertNotEmpty(json_decode($response->content(),true)['result']['api_token']);

        return json_decode($response->content(),true)['result']['api_token'];
    }

    public function test_unit_unauthenticated_user()
    {       
        //Submit post request with autorizathion header
        $response = $this->get('api/items');
        
        // Verify status 200 
        $response->assertStatus(401);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Unauthenticated.']);
         
    }

    //TEST FUNCTION index

    public function test_unit_get_all()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->get('api/units');
        
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
          ]);

        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Units retrieved successfully.']);
         
    }

    
}
