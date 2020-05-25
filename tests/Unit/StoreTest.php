<?php

namespace Tests\Unit;

use App\Store;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->setBaseRoute('stores');
        $this->setBaseModel('App\Store');
        $this->setFaker(); 
        $this->setFieldsDatabaseHas(['id', 'name', 'country_id']);  
    }

    //CREATE
    public function test_store_create_name_required()
    {   
        $this->store_name_required('C');
    }

    public function test_store_create_name_max()
    {   
        $this->store_name_max('C');
    }

    public function test_store_create_country_id_invalid()
    {   
        $this->store_country_id_invalid('C');
    }

    public function test_store_create_ok()
    {   
        // Set Database has      
        $this->setDatabaseHas(true);
        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'Store created successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->create();
    }

    //UPDATE
    public function test_store_update_name_required()
    {   
        $this->store_name_required('U');
    }

    public function test_store_update_name_max()
    {   
        $this->store_name_max('U');
    }

    public function test_store_update_country_id_invalid()
    {   
        $this->store_country_id_invalid('U');
    }

    public function test_store_update_ok()
    {   
        // Set Database has      
        $this->setDatabaseHas(true);
        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'Store updated successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->update();
    }

    //DELETE
    public function test_store_delete_ok()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Store deleted successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->softDestroy();   
         
    }

    

    //Share functions to create/update
    private function store_name_required($option = ''){
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

    private function store_name_max($option = ''){
        $this->checkOptionCRUD($option);
        // Set Database has      
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The name may not be greater than 45 characters.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['name' => $this->faker->regexify('[A-Za-z0-9]{50}')]);
                break;
            case 'U': $this->update(['name' => $this->faker->regexify('[A-Za-z0-9]{50}')]);
                break;
        }
    }

    private function store_country_id_invalid($option = ''){
        $this->checkOptionCRUD($option);
        // Set Database has      
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The selected country id is invalid.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        switch ($option) {
            case 'C': $this->create(['country_id' => '0']);
                break;
            case 'U': $this->update(['country_id' => '0']);
                break;
        }
    }


}
