<?php

namespace Tests\Unit;

use App\Invoice;
use App\InvoiceDetail;
use App\User;
use App\Item;
use Faker;
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


     //TEST FUNCTION create invoice detail

    public function test_invoice_detail_create_item_id_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice detail object
        $invoiceDetail = factory(InvoiceDetail::class)->make(['item_id' => '']);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/invoiceDetails', $invoiceDetail->toArray());

        //Verify in the database
        $this->assertDatabaseMissing('invoice_details', $invoiceDetail->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The item id field is required.']);
    }

    public function test_invoice_detail_create_quantity_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice detail object
        $invoiceDetail = factory(InvoiceDetail::class)->make(['quantity' => '0']);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/invoiceDetails', $invoiceDetail->toArray());

        //Verify in the database
        $this->assertDatabaseMissing('invoice_details', $invoiceDetail->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The quantity must be at least 1.']);
    }

    public function test_invoice_detail_create_price_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice detail object
        $invoiceDetail = factory(InvoiceDetail::class)->make(['price' => '']);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/invoiceDetails', $invoiceDetail->toArray());

        //Verify in the database
        $this->assertDatabaseMissing('invoice_details', $invoiceDetail->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The price field is required.']);
    }

    public function test_invoice_detail_create_invoice_id_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice detail object
        $invoiceDetail = factory(InvoiceDetail::class)->make(['invoice_id' => '']);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/invoiceDetails', $invoiceDetail->toArray());

        //Verify in the database
        $this->assertDatabaseMissing('invoice_details', $invoiceDetail->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The invoice id field is required.']);
    }

    public function test_invoice_detail_create_without_stock()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice detail object
        $invoiceDetail = factory(InvoiceDetail::class)->make();

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/invoiceDetails', $invoiceDetail->toArray());

        //Verify in the database
        $this->assertDatabaseMissing('invoice_details', $invoiceDetail->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'There is not stock for product '.$invoiceDetail->item_id]);
    }

    public function test_invoice_detail_create_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        $faker = Faker\Factory::create();

        // Generate a invoice detail object
        $item = factory(Item::class)->create(['stock' => $faker->randomNumber(4, $strict = true)]);
        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());

        $invoiceDetail = factory(InvoiceDetail::class)->make(['item_id' => $item->id]);

        // update the stock
        $item->stock = $item->stock - $invoiceDetail->quantity;
        
        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->post('api/invoiceDetails', $invoiceDetail->toArray());

                        
        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('invoice_details', $invoiceDetail->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
            ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Invoice detail created successfully.']);
    }


    // FUNCTION: update

    public function test_invoice_updated_invoice_id_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice detail object
        $sellInvoice = factory(Invoice::class)->create(['serie' => '', 'type' => 'S']);

        //Verify in the database
        $this->assertDatabaseHas('invoices', $sellInvoice->toArray());

         // Set values to Update
        $sellInvoice->serie = 'AB-123';

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->put('api/invoices/0',  $sellInvoice->toArray());

        //Verify in the database
        $this->assertDatabaseMissing('invoices', $sellInvoice->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Invoice not found.']);
    }

    public function test_invoice_updated_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a invoice detail object
        $sellInvoice = factory(Invoice::class)->create(['serie' => '', 'type' => 'S']);

        //Verify in the database
        $this->assertDatabaseHas('invoices', $sellInvoice->toArray());

         // Set values to Update

        // Set values to Update
        $newSellInvoice     = factory(Invoice::class)->make();
        $sellInvoice->serie = $newSellInvoice->serie;

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
                         ->put('api/invoices/' . $sellInvoice->id,  $sellInvoice->toArray());

        //Verify in the database
        $this->assertDatabaseHas('invoices', $sellInvoice->toArray());

        // Verify status 200
        $response->assertStatus(200);

        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Sell invoice updated successfully.']);
    }

    //FUNCTION delete/anul
    public function test_invoice_anull_id_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/invoices/' . '0');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Invoice not found.']);
         
    }

    public function test_invoice_anull_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a item 
        $invoice = factory(Invoice::class)->create(['type' => 'S']);
        
        //Verify in the database
        $this->assertDatabaseHas('invoices', $invoice->toArray());

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/invoices/' . $invoice->id);
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Invoice Anulled/Canceled successfully.']);
         
    }


}
