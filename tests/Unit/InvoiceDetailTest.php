<?php

namespace Tests\Unit;

use App\InvoiceDetail; 
use App\Item;
use App\Invoice;
use App\Store;
use App\Unit;

use Carbon\Carbon;
use Tests\TestCase;

class InvoiceDetailTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->setBaseRoute('invoiceDetails');
        $this->setBaseModel('App\InvoiceDetail');
        $this->setFaker();   
    }

    //TEST FUNCTION create invoice detail

    public function test_invoice_detail_create_item_id_is_required()
    {
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The item id field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $this->create(['item_id' => '']);
    }

    public function test_invoice_detail_create_item_must_be_in_db()
    {
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The selected item id is invalid.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $this->create(['item_id' => $this->faker->randomNumber(4, $strict = true)]);
    }

    public function test_invoice_detail_create_quantity_is_required()
    {
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The quantity field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $this->create(['quantity' => '']);
    }

    public function test_invoice_detail_create_quantity_must_be_numeric()
    {
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The quantity must be a number.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $this->create(['quantity' => $this->faker->lexify('??')]);
    }

    public function test_invoice_detail_create_quantity_must_be_gt0()
    {
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The quantity must be greater than 0.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $this->create(['quantity' => 0]);
    }

    public function test_invoice_detail_create_invoice_id_is_required()
    {
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The invoice id field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $this->create(['invoice_id' => '']);
    }

    public function test_invoice_detail_create_invoice_id_must_be_in_db()
    {
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The selected invoice id is invalid.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $this->create(['invoice_id' => $this->faker->randomNumber(2, $strict = true)]);
    }

    public function test_invoice_detail_create_without_stock()
    {
        //Authentication
        $this->get_api_token();

        //models needed
        $item = factory(Item::class)->create(['store_id' => auth()->user()->store_id, 'stock' => 0, 'stocked' => true]);

        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'There is not stock for item '. $item->id]);
        $this->setAssertJson($assertsJson);

        //run option
        $this->create(['item_id' => $item->id]);
    }

    public function test_invoice_detail_create_check_invoice_date()
    {
        //Authentication
        $this->get_api_token();

        //models needed
        $item = factory(Item::class)->create(['store_id' => auth()->user()->store_id,'stock' => $this->faker->randomNumber(3, $strict = true) ]);
        // $invoice = factory(Invoice::class)->create(['created_at' => Carbon::now()->subDay() ]);
        $invoice = factory(Invoice::class)->create(['created_at' => Carbon::now()->subMinute(2) ]);

        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'Invoice is out the date range.' ]);
        $this->setAssertJson($assertsJson);

        //run option
        $response = $this->create(['item_id' => $item->id, 'invoice_id' => $invoice->id]);

    }

    public function test_invoice_detail_create_item_belongs_another_store()
    {
        //models needed
        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(3, $strict = true)]);
        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'Item does not belong to current store.' ]);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();
        
        //run option
        $response = $this->create(['item_id' => $item->id]);

    }

    public function test_invoice_detail_create_invoice_belongs_another_store()
    {
        //Authentication
        $this->get_api_token();

        //models needed
        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(3, $strict = true),
                                              'store_id' => auth()->user()->store_id]);

        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'Invoice does not belong to current store.' ]);
        $this->setAssertJson($assertsJson);

        //run option
        $response = $this->create(['item_id' => $item->id]);

    }


    public function test_invoice_detail_create_its_greater_than_invoice()
    {
        //Authentication
        $this->get_api_token();

        //models needed
        $invoice = factory(Invoice::class)->create(['store_id' => auth()->user()->store_id,
                                                    'subtotal' => $this->faker->randomNumber(1, $strict = true)]);

        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(3, $strict = true),
                                              'store_id' => auth()->user()->store_id]);

        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'Invoice Detail total is greater than Invoice subtotal.' ]);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $response = $this->create(['item_id' => $item->id, 'invoice_id' => $invoice->id]);

    }

    public function test_invoice_detail_create_ok_item_not_stocked()
    {
        //Authentication
        $this->get_api_token();

        //models needed
        $item = factory(Item::class)->create(['store_id' => auth()->user()->store_id, 'stock' => 0, 'stocked' => false]);
        $invoice = factory(Invoice::class)->create(['store_id' => auth()->user()->store_id ]);
        

        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Invoice detail created successfully.' ]);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $response = $this->create(['item_id' => $item->id, 'price' => $item->price, 'invoice_id' => $invoice->id,
                                   'quantity' => 1]);

        //Check that invoice details was created successfully
        $this->assertDatabaseHas('invoice_details', json_decode($response->content(),true)['result']);

        //Check that stock is updated
        $itemUpdate = Item::findOrFail($item->id);

        $this->assertEquals($itemUpdate->stock, 0);
    }

    public function test_invoice_detail_create_ok()
    {
        //Authentication
        $this->get_api_token();

        //models needed
        $invoice = factory(Invoice::class)->create(['store_id' => auth()->user()->store_id ]);
                                                    // 'sub' => $this->faker->randomNumber(3, $strict = true)]);
        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(3, $strict = true), 'stocked' => true,
                                              'store_id' => auth()->user()->store_id]);

        //set db checking
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Invoice detail created successfully.' ]);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //run option
        $response = $this->create(['item_id' => $item->id, 'price' => $item->price, 'invoice_id' => $invoice->id,
                                   'quantity' => 1]);

        //Check that invoice details was created successfully
        $this->assertDatabaseHas('invoice_details', json_decode($response->content(),true)['result']);

        //Check that stock is updated
        $itemUpdate = Item::findOrFail($item->id);

        $this->assertLessThan($item->stock, $itemUpdate->stock);
    }


    //TESTING MODEL

    public function test_invoice_detail_calculate_total()
    {
        $invoice_detail = factory(InvoiceDetail::class)->make();

        $invoice_detail->calculateTotal();

        $invoice_detail->save();

        $this->assertDatabaseHas('invoice_details', $invoice_detail->toArray());
    }
}
