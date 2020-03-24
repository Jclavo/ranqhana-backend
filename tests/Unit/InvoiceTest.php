<?php

namespace Tests\Unit;

use App\Invoice;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed database
        $this->seed();
        // Artisan::call('db:seed');
       
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

     //TEST FUNCTION create sell invoice

    public function test_sell_invoice_create_subtotal_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice object
        $invoice = factory(Invoice::class)->make(['subtotal' => '0']);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/sellInvoices', $invoice->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('invoices', $invoice->toArray());

         // Verify status 200 
         $response->assertStatus(200);
        
         // Verify values in response
         $response->assertJson(['status' => false]);
         $response->assertJson(['message' => 'The subtotal must be between 0.01 and 99999.99.']);
    } 

    // public function test_sell_invoice_create_total_is_required()
    // {
    //     // get api token from authenticate user
    //     $api_token = $this->get_api_token();

    //     // Generate a invoice object
    //     $invoice = factory(Invoice::class)->make(['total' => '0']);

    //     //Submit post request with autorizathion header
    //     $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
    //                      ->post('api/sellInvoices', $invoice->toArray());
        
    //     //Verify in the database
    //     $this->assertDatabaseMissing('invoices', $invoice->toArray());

    //      // Verify status 200 
    //      $response->assertStatus(200);
        
    //      // Verify values in response
    //      $response->assertJson(['status' => false]);
    //      $response->assertJson(['message' => 'The total must be between 0.01 and 99999.99.']);
    // } 

    public function test_sell_invoice_create_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice object
        $invoice = factory(Invoice::class)->make(['type' => 'S']);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/sellInvoices', $invoice->toArray());
        
        //Verify in the database
        $this->assertDatabaseHas('invoices', $invoice->toArray());

        //Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJsonStructure([
        'status',
        'message',
        'result' => []
        ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Sell invoice created successfully.']);
    } 
}
