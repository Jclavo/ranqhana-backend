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
        $response = $this->get('api/units');
        
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

    //TEST FUNCTION create/store

    public function test_unit_create_code_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate an unit object
        $unit = factory(Unit::class)->make(['code' => '']);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/units', $unit->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('units', $unit->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The code field is required.']);   
    }

    public function test_unit_create_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate an unit object
        $unit = factory(Unit::class)->make();

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/units', $unit->toArray());
        
        //Verify in the database
        // $this->assertDatabaseHas('units', $unit->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => [],
            'records',
            ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Units created successfully.']);   
    }

    //TEST FUNCTION show

    public function test_unit_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->get('api/units/' . '0');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Unit not found.']);
         
    }

    public function test_unit_show_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        // Generate a item object
        $unit = factory(Unit::class)->create();

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->get('api/units/' . $unit->id);
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Unit retrieved successfully.']);
         
    }

    //TEST FUNCTION update
    public function test_unit_update_code_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        // Generate an unit object
        $unit = factory(Unit::class)->create();

        //Verify in the database
        $this->assertDatabaseHas('units', $unit->toArray());

        // Generate an unit object to edit
        $newUnit = factory(Unit::class)->make(['code' => '']);

        $unit->code = $newUnit->code;

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
             ->put('api/units/' . $unit->id ,  $unit->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('units', $unit->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The code field is required.']);   
    }

    public function test_unit_update_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        // Generate an unit object
        $unit = factory(Unit::class)->create();

        //Verify in the database
        $this->assertDatabaseHas('units', $unit->toArray());

        // Generate an unit object to edit
        $newUnit = factory(Unit::class)->make();

        $unit->code = $newUnit->code;
        $unit->description = $newUnit->description;
        $unit->allow_decimal = $newUnit->allow_decimal;

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
             ->put('api/units/' . $unit->id ,  $unit->toArray());
        
        //Verify in the database
        $this->assertDatabaseHas('units', $unit->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Units created successfully.']);   
    }

    //TEST FUNCTION delete

    public function test_unit_delete_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
       
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/units/' . '0');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Unit not found.']);
         
    }

    public function test_unit_delete_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate an unit object
        $unit = factory(Unit::class)->create();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/units/' . $unit->id);
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Unit deleted successfully.']);
         
    }
}
