<?php

namespace Tests\Unit;

use App\Invoice;
use App\InvoiceDetail; 
use App\User;
use App\Item;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
 
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->setBaseRoute('invoices');
        $this->setBaseModel('App\Invoice');
        $this->setFaker();   
    }


    //TEST FUNCTION create sell invoice
    
    public function test_sell_invoice_create_subtotal_is_required()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The subtotal field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['subtotal' => '']);
    }

    public function test_sell_invoice_create_subtotal_must_be_a_number()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The subtotal must be a number.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['subtotal' => $this->faker->lexify('?')]);
    }

    public function test_sell_invoice_create_subtotal_must_be_a_number_gt0()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The subtotal must be greater than 0.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['subtotal' => $this->faker->randomNumber(2) * - 1]);
    }

    public function test_sell_invoice_create_taxes_must_be_a_number()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The taxes must be a number.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['taxes' => '']);
    }

    public function test_sell_invoice_create_taxes_must_be_a_number_gte0()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The taxes must be greater than or equal 0.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['taxes' => $this->faker->randomNumber(2) * - 1]);
    }

    public function test_sell_invoice_create_taxes_must_be_less_than_subtotal()
    {
        //some values needed
        $subtotal = $this->faker->randomNumber(2, $strict = true);

        // db config
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The taxes must be less than ' . $subtotal . '.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['subtotal' => $subtotal, 'taxes' => $subtotal]);
    }

    public function test_sell_invoice_create_discount_must_be_a_number_gte0()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The discount must be greater than or equal 0.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['discount' => $this->faker->randomNumber(2) * - 1]);
    }

    public function test_sell_invoice_create_discount_must_be_a_number()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The discount must be a number.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['discount' => '']);
    }

    public function test_sell_invoice_create_discount_must_be_less_than_subtotal()
    {
        //some values needed
        $subtotal = $this->faker->randomNumber(2, $strict = true);

        // db config
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The discount must be less than ' . $subtotal . '.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['subtotal' => $subtotal, 'discount' => $subtotal]);
    }

    public function test_sell_invoice_create_type_is_required()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The type id field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['type_id' => '']);
    }

    public function test_sell_invoice_create_type_must_be_in_table()
    {
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The selected type id is invalid.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['type_id' => $this->faker->randomNumber(2, $strict = true)]);
    }


    public function test_sell_invoice_create_not_mandatory_empty_fields()
    {
        // db config
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'Sell invoice created successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['type_id' => 1, 'serie' => null, 'taxes' => null, 'discount' => null,
                       'user_id' => auth()->user()->id, 'store_id' => auth()->user()->store_id ]);
    }

    public function test_sell_invoice_create_ok()
    {       
        // db config
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'Sell invoice created successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        $this->create(['type_id' => 1, 'user_id' => auth()->user()->id, 'store_id' => auth()->user()->store_id]);
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
