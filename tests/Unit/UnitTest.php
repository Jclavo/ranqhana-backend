<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Unit;

use Illuminate\Support\Facades\Auth as Auth;
use Tests\TestCase;

class UnitTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();    
        
        $this->setBaseRoute('units');
        $this->setBaseModel('App\Models\Unit');
        $this->setFaker();
        $this->setFieldsDatabaseHas(['id', 'code', 'description', 'fractioned']);  
    
    }

    public function test_unit_unauthenticated_user()
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

    // public function test_unit_get_all()
    // {
    //     //Set values to Response
    //     $this->setAssertStatus(200);
       
    //     //Set Json asserts
    //     $assertsJson = array();
    //     array_push($assertsJson,['status' => true]);
    //     array_push($assertsJson,['message' => 'Unit retrieved successfully.']);
    //     $this->setAssertJson($assertsJson);

    //     //Authentication
    //     $this->get_api_token();

    //     //Action
    //     $this->read();
         
    // }

    //TEST FUNCTION create/store

    public function test_unit_create_code_is_required()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['result' => []]);
        array_push($assertsJson,['message' => 'The code field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->create(['code' => '']);
    }

    public function test_unit_create_code_max()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'The code may not be greater than 3 characters.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->create(['code' => $this->faker->lexify('????')]);
    }

    public function test_unit_create_code_repeated()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'The code has already been taken.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Generate one
        $unit = factory(Unit::class)->create();

        //Action
        $this->create(['code' => $unit->code]);
    }

    public function test_unit_create_ok_with_empty_not_mandatory_fields()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Unit created successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Generate one
        $unit = factory(Unit::class)->create();

        //Action
        $this->create(['description' => null,
                       'fractioned' => 0]);
    }

    public function test_unit_create_ok()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Unit created successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->create();
    }


    //TEST FUNCTION show

    public function test_unit_show_ok()
    {     
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Unit retrieved successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->readBy();

    }

    //TEST FUNCTION update

    public function test_unit_update_code_is_required()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'The code field is required.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->update(['code' => '']);
    }

    public function test_unit_update_code_repeated()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => false]);
        array_push($assertsJson,['message' => 'The code has already been taken.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Generate one
        $unit = factory(Unit::class)->create();

        //Action
        $this->update(['code' => $unit->code]);
    }

    public function test_unit_update_ok_with_empty_not_mandatory_fields()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Unit updated successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Generate one
        $unit = factory(Unit::class)->create();

        //Action
        $this->update(['description' => null,
                       'fractioned' => 0]);
    }

    public function test_unit_update_ok()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Unit updated successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->update();   
    }

    //TEST FUNCTION delete

    public function test_unit_delete_ok()
    {
        // Set Database has
        $this->setDatabaseHas(false);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Unit deleted successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->destroy();   
    }
}
