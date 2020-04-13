<?php

namespace Tests\Unit;

use App\User;
use App\Unit;
use Illuminate\Support\Facades\Auth as Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();    
        
        $this->setBaseRoute('units');
        $this->setBaseModel('App\Unit');
        $this->setFaker();
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

    public function test_unit_get_all()
    {
        //Set values to Response
        $this->setAssertStatus(200);
       
        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Units retrieved successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->read();
         
    }

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
        $unit = factory(Unit::class)->create(['store_id' => auth()->user()->store_id]);

        //Action
        $this->create(['code' => $unit->code,
                       'store_id' => auth()->user()->store_id]);
    }

    public function test_unit_create_ok()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Units created successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->create(['store_id' => auth()->user()->store_id]);
    }


    //TEST FUNCTION show

    public function test_unit_show_from_another_store()
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
        // $this->create(['store_id' => auth()->user()->store_id]);
        $this->readBy();
    }

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
        $this->readBy(['store_id' => auth()->user()->store_id]);

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

    public function test_unit_update_from_another_store()
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
        $unit = factory(Unit::class)->create(['store_id' => auth()->user()->store_id]);

        //Action
        $this->update(['code' => $unit->code,
                       'store_id' => auth()->user()->store_id],
                       ['store_id' => auth()->user()->store_id] //attribute mandatory
                    );
    }

    public function test_unit_update_ok()
    {
        // Set Database has
        $this->setDatabaseHas(true);

        //Set Json asserts
        $assertsJson = array();
        array_push($assertsJson,['status' => true]);
        array_push($assertsJson,['message' => 'Units updated successfully.']);
        $this->setAssertJson($assertsJson);

        //Authentication
        $this->get_api_token();

        //Action
        $this->update(['store_id' => auth()->user()->store_id],
                      ['store_id' => auth()->user()->store_id] //attribute mandatory 
                    );   
    }

    //TEST FUNCTION delete

    public function test_unit_delete_from_another_store()
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
        $this->destroy(['store_id' => auth()->user()->store_id]);   
    }
}
