<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\User;
use App\Models\Price;
use App\Models\Invoice;

use Tests\TestCase;
use App\Http\Controllers\API\ItemController;

class ItemTest extends TestCase
{ 

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->setBaseRoute('items');
        $this->setBaseModel('App\Models\Item');
        $this->setFaker();
        $this->setFieldsDatabaseHas(['id', 'name', 'price', 'stocked',
                                      'unit_id', 'user_id']); 
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

    // public function test_item_get_all()
    // {
    //     $this->setAssertStatus(200);
       
    //     //Set Json asserts
    //     $assertsJson = array();
    //     array_push($assertsJson,['status' => true]);
    //     array_push($assertsJson,['message' => 'Items retrieved successfully.']);
    //     $this->setAssertJson($assertsJson);

    //     //Authentication
    //     $this->get_api_token();

    //     //Action
    //     $this->read();
         
    // }

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
        $this->create();
    }


    //TEST FUNCTION show

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
        $this->readBy();
         
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

    // public function test_item_update_stocked_ok()
    // {
    //     // get api token from authenticate user
    //     $this->get_api_token();
        
    //      // Generate a item object
    //     $item = factory(Item::class)->create(['stocked' => false]);

    //     //Verify in the database
    //     $this->setResultResponseSimple($item->toArray());
    //     $this->checkRecordInDB();

    //     // Set values to Update
    //     $newItem = factory(Item::class)->make(['stocked' => true]);

    //     $item->stocked = $newItem->stocked;

    //     //Submit post request with autorizathion header
    //     $response = 
        
    //     $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
    //           ->put('api/items/' . $item->id ,  $item->toArray());
        
    //     //Verify in the database
    //     $this->setResultResponseSimple($item->toArray());
    //     $this->checkRecordInDB();

    //     // Verify status 200 
    //     $response->assertStatus(200);
        
    //     // Verify values in response
    //     $response->assertJson(['status' => true]);
    //     $response->assertJson(['message' => 'Item updated successfully.']);
         
    // }

    // public function test_item_update_with_empty_not_required_fields()
    // {
    //     $this->item_with_empty_not_required_fields('U');
    // }

    public function test_item_update_ok()
    {
        // // Set Database has
        // $this->setDatabaseHas(true);

        // //Set Json asserts
        // $assertsJson = array();
        // array_push($assertsJson,['status' => true]);
        // array_push($assertsJson,['message' => 'Item updated successfully.']);
        // $this->setAssertJson($assertsJson);

        // //Authentication
        // $this->get_api_token();

        // //Action
        // $this->update();   
    }

    //TEST FUNCTION delete

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
        $this->softDestroy();   
         
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
            case 'C': $this->create(['price' => $this->faker->randomNumber(2, $strict = true) * - 1]);
            break;
            case 'U': $this->update(['price' => $this->faker->randomNumber(2, $strict = true) * - 1]);
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
        array_push($assertsJson,['message' => 'The selected unit id is invalid.']);
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

    public function item_with_empty_not_required_fields($option = '')
    {
        $this->checkOptionCRUD($option);   
        // Set Database has
        $this->setDatabaseHas(true);

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
                $this->update(['stocked' => '', 'description' => ''],
                              ['stock' => 0 ] //attribute mandatory 
                    );
                break;
        }
    }


    //TESTING MODEL
    public function test_item_has_stock()
    {
        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(4, $strict = true)]);

        $this->assertTrue($item->hasStock());
    }

    public function test_item_increase_stock()
    {
        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(3, $strict = true)]);

        $item->increaseStock($this->faker->randomNumber(1, $strict = true));

        $item->save();

        $this->setResultResponseSimple($item->toArray());
        $this->checkRecordInDB();
    }

    public function test_item_decrease_stock()
    {
        $item = factory(Item::class)->create(['stock' => $this->faker->randomNumber(3, $strict = true)]);

        $item->decreaseStock($this->faker->randomNumber(1, $strict = true));

        $item->save();

        $this->setResultResponseSimple($item->toArray());
        $this->checkRecordInDB();
    }
    

}
