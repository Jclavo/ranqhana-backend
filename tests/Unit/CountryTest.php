<?php

namespace Tests\Unit;

use App\Models\Country;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed database
        $this->seed();
       
    }

    public function test_country_get_all()
    {
        //Submit post request to create an user endpoint
        $response = $this->get('api/country');
        
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
        $response->assertJson(['message' => 'Countries retrieved successfully.']);
         
    }
}
