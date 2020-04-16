<?php

namespace Tests\Unit;

use App\Item;
use App\User;
use App\Price;
use Tests\TestCase;
use App\Http\Controllers\API\ItemController;

class ItemTest extends TestCase
{ 

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->setBaseRoute('items');
        $this->setBaseModel('App\Item');
        $this->setFaker();   
    }

    public function test_item_unauthenticated_user()
    {       
        //Set values to Response
        $this->setAssertStatus(401);

        //Set Json structure
        $this->setAssertJsonStructure([]);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'Unauthenticated.']);
        $this->setAssertJson($assertsJson);

        //Action
        $this->read();
         
    }

    //TEST FUNCTION index

    public function test_item_get_all()
    {
        $this->setAssertStatus(200);
       
        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Items retrieved successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->read();
         
    }

    //TEST FUNCTION create/store

    public function test_item_create_name_is_required()
    {
        $this->item_name_is_required('C'); 
    }

    public function test_item_create_price_is_required()
    {
        $this->item_price_is_required('C'); 
    }

    public function test_item_create_price_must_be_a_number()
    {
        $this->item_price_must_be_a_number('C');
    }

    public function test_item_create_price_must_be_a_positive_number()
    {
        $this->item_price_must_be_a_positive_number('C');
    }

    public function test_item_create_unit_not_found()
    {
        $this->item_unit_not_found('C');
    }

    public function test_item_create_unit_required()
    {
        $this->item_unit_required('C');
    }

    public function test_item_create_unit_is_zero()
    {
        $this->item_unit_is_zero('C');
    }

    public function test_item_create_with_empty_not_required_fields()
    {
        $this->item_with_empty_not_required_fields('C');
    }

    public function test_item_create_ok()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Item created successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->create(['store_id' => auth()->user()->store_id]);
    }


    //TEST FUNCTION show

    public function test_item_show_from_another_store()
    {     
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'This action is unauthorized.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->readBy();
    }

    public function test_item_show_by_id()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Item retrieved successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->readBy(['store_id' => auth()->user()->store_id]);
         
    }

    //TEST FUNCTION update

    public function test_item_update_name_is_required()
    {
        $this->item_price_is_required('U');         
    }

    public function test_item_update_price_is_required()
    {
        $this->item_price_is_required('U'); 
    }

    public function test_item_update_price_must_be_a_number()
    {
        $this->item_price_must_be_a_number('U');
    }

    public function test_item_update_price_must_be_a_positive_number()
    {
        $this->item_price_must_be_a_positive_number('U');
    }

    public function test_item_update_unit_not_found()
    {
        $this->item_unit_not_found('U');
    }

    public function test_item_update_unit_required()
    {
        $this->item_unit_required('U');
    }

    public function test_item_update_unit_is_zero()
    {
        $this->item_unit_is_zero('U');
    }

    public function test_item_update_price_ok()
    {
        // get api token from authenticate user
        $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->create(['store_id' => auth()->user()->store_id]);

        // Generate a price object
        $price = factory(Price::class)->create(['price' => $item->price,
                                                'item_id' => $item->id
                                                ]);

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('prices', $price->toArray());

        // Set values to Update
        $newItem = factory(Item::class)->make(['store_id' => auth()->user()->store_id]);

        $item->price = $newItem->price;

         // Generate a price object
        $price = factory(Price::class)->make(['price' => $item->price,
                                              'item_id' => $item->id
                                            ]);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
              ->put('api/items/' . $item->id ,  $item->toArray());
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Item updated successfully.']);  

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('prices', $price->toArray());
    }

    public function test_item_update_stocked_not_change()
    {
        // get api token from authenticate user
        $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(2), 
                                              'stocked' => true,
                                              'store_id' => auth()->user()->store_id
                                            ]);

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        // $this->assertDatabaseHas('prices', $price->toArray());

        // Set values to Update
        $newItem = factory(Item::class)->make(['stocked' => false,
                                               'store_id' => auth()->user()->store_id
                                             ]);

        $item->stocked = $newItem->stocked;

         // Generate a price object
        $price = factory(Price::class)->make(['price' => $item->price,
                                              'item_id' => $item->id]);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
              ->put('api/items/' . $item->id ,  $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The item has stock. It can not be modified.']);
         
    }

    public function test_item_update_stocked_ok()
    {
        // get api token from authenticate user
        $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->create(['stocked' => false, 'store_id' => auth()->user()->store_id]);

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());

        // Set values to Update
        $newItem = factory(Item::class)->make(['stocked' => true, 'store_id' => auth()->user()->store_id]);

        $item->stocked = $newItem->stocked;

         // Generate a price object
        $price = factory(Price::class)->make(['price' => $item->price,
                                              'item_id' => $item->id]);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
              ->put('api/items/' . $item->id ,  $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Item updated successfully.']);
         
    }

    public function test_item_update_from_another_store()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'This action is unauthorized.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->update();
    }

    public function test_item_update_with_empty_not_required_fields()
    {
        $this->item_with_empty_not_required_fields('U');
    }

    public function test_item_update_ok()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Item updated successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->update(['store_id' => auth()->user()->store_id],
                      ['store_id' => auth()->user()->store_id] //attribute mandatory 
                    );   
    }

    //TEST FUNCTION delete

    public function test_item_delete_from_another_store()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'This action is unauthorized.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->destroy();
         
    }

    public function test_item_delete_ok()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Item deleted successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->softDestroy(['store_id' => auth()->user()->store_id]);   
         
    }

    //TEST FUNCTION savePrice
    public function test_item_save_price_first_value_for_item()
    {
        $item = factory(Item::class)->create();

        $itemController = new ItemController();

        $price = $itemController->savePrice(20.5,$item->id);
        // $this->assertEquals(true, $appVersion->isValidVersion($version));

        $this->assertNotNull($price);
        //Verify in the database
        $this->assertDatabaseHas('prices', $price->toArray());

    }

    public function test_item_save_price_repeated_not_save()
    {
        $item = factory(Item::class)->create();

        $itemController = new ItemController();

        $itemController->savePrice(20.5,$item->id);
        
        $price = $itemController->savePrice(20.5,$item->id);

        $this->assertNull($price);

    }

    public function test_item_save_price()
    {
        $item = factory(Item::class)->create();

        $itemController = new ItemController();

        $itemController->savePrice(20.5,$item->id);
        
        $itemController->savePrice(10,$item->id);

        $price = $itemController->savePrice(20,$item->id);

        $this->assertNotNull($price);
        //Verify in the database
        $this->assertDatabaseHas('prices', $price->toArray());

    }

    public function test_item_save_price_null()
    {
        $item = factory(Item::class)->create();

        $itemController = new ItemController();
       
        $price = $itemController->savePrice(0,$item->id);

        $this->assertNull($price);

    }

    public function test_item_save_price_negative()
    {
        $item = factory(Item::class)->create();

        $itemController = new ItemController();
       
        $price = $itemController->savePrice(-55,$item->id);

        $this->assertNull($price);

    }

    //FUNCTION: updateStock

    public function test_item_update_stock_item_not_found(){

        $item = factory(Item::class)->create();

        $itemController = new ItemController();

        $stock = $itemController->updateStock(0, 10);

        $this->assertFalse(json_decode($stock->content(),true)['status']);
        $this->assertEquals(json_decode($stock->content(),true)['message'], 'Item not found.');
    }

    public function test_item_update_stock_without_stock(){

        $item = factory(Item::class)->create();

        $itemController = new ItemController();

        $stock = $itemController->updateStock($item->id, 10);

        $this->assertFalse(json_decode($stock->content(),true)['status']);
        $this->assertEquals(json_decode($stock->content(),true)['message'], 'There is not stock for product '. $item->id);
        // $this->assertTrue($stock);
    }

    public function test_item_update_stock_ok(){

        $item = factory(Item::class)->create(['stock' => 100]);

        $itemController = new ItemController();

        $quantitySold = 10;
        $stock = $itemController->updateStock($item->id, $quantitySold);

        $item->stock = $item->stock - $quantitySold;

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());

        $this->assertTrue(json_decode($stock->content(),true)['status']);
        $this->assertEquals(json_decode($stock->content(),true)['message'], 'Stock updated.');
        // $this->assertTrue($stock);
    }


    //PRIVATE
    private function item_name_is_required($option = '')
    {
        $this->checkOptionCRUD($option);      
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The name field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['name' => '']);
                break;
            case 'U': $this->update(['name' => '']);
                break;
        }
    }

    private function item_price_is_required($option = '')
    {
        $this->checkOptionCRUD($option);      
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The price field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['price' => '']);
                break;
            case 'U': $this->update(['price' => '']);
                break;
        }
    }

    private function item_price_must_be_a_number($option = '')
    {
        $this->checkOptionCRUD($option);   
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The price must be a number.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['price' => $this->faker->lexify('???')]);
            break;
            case 'U': $this->update(['price' => $this->faker->lexify('???')]);
            break;
        }
    }

    public function item_price_must_be_a_positive_number($option = '')
    {
        $this->checkOptionCRUD($option);   
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The price must be between 0.00 and 99999.99.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['price' => $this->faker->randomNumber(2) * - 1]);
            break;
            case 'U': $this->update(['price' => $this->faker->randomNumber(2) * - 1]);
            break;
        }
    }

    private function item_unit_not_found($option = '')
    {
        $this->checkOptionCRUD($option);   
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        $unit_id = $this->faker->randomNumber(4, $strict = true);
        array_push($assertsJson,['message' => 'No query results for model [App\\Unit] ' . $unit_id]);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['unit_id' => $unit_id]);
            break;
            case 'U': $this->update(['unit_id' => $unit_id]);
            break;
        }
    }

    private function item_unit_required($option = '')
    {
        $this->checkOptionCRUD($option);   
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'The unit id field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['unit_id' => '']);
            break;
            case 'U': $this->update(['unit_id' => '']);
            break;
        }
    }

    private function item_unit_is_zero($option = '')
    {
        $this->checkOptionCRUD($option);   
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'The unit id must be greater than 0.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['unit_id' => 0]);
            break;
            case 'U': $this->update(['unit_id' => 0]);
            break;
        }
    }

    public function item_with_empty_not_required_fields($option = '')
    {
        $this->checkOptionCRUD($option);   
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C':
                array_push($assertsJson,['message' => 'Item created successfully.']);
                $this->setAssertJson($assertsJson); 
                $this->create(['stocked' => '', 'description' => '']);
                break;
            case 'U': 
                array_push($assertsJson,['message' => 'Item updated successfully.']);
                $this->setAssertJson($assertsJson); 
                $this->update(['store_id' => auth()->user()->store_id, 'stocked' => '', 'description' => ''],
                                    ['store_id' => auth()->user()->store_id] //attribute mandatory 
                                    );
                break;
        }
    }

}
