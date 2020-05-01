<?php

namespace Tests;

use App\User;
use App\Store;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\TestCaseBase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker;

abstract class TestCase extends TestCaseBase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $baseRoute = null;
    protected $baseModel = null;
    protected $assertStatus = 200;
    protected $assertJsonStructure = [];
    protected $fieldsDatabaseHas = [];
    protected $resultResponse = null;
    protected $response = null;
    // protected $assertJsonStructure = [
    //     'status',
    //     'message',
    //     'result' => [],
    //     'records'
    //   ];
    protected $assertJson = [];
    protected $databaseHas = true;
    protected $faker = null;

    function setFaker(){
        $this->faker = Faker\Factory::create();
    }

    protected function setBaseRoute($route){
        $this->baseRoute = 'api/' . $route . '/';
    }

    protected function setBaseModel($model){
        $this->baseModel = $model;
    }

    protected function setAssertStatus($assertStatus){
        $this->assertStatus = $assertStatus;
    }

    protected function setAssertJson($assertJson){
        $this->assertJson = $assertJson;
    }

    protected function setAssertJsonStructure($assertJsonStructure = []){
        $this->assertJsonStructure = $assertJsonStructure;
    }

    protected function setDatabaseHas($databaseHas = true){
        $this->databaseHas = $databaseHas;
    }

    protected function setFieldsDatabaseHas($fieldsDatabaseHas = []){
        $this->fieldsDatabaseHas = $fieldsDatabaseHas;
    }

    protected function setResultResponse($response = null){

        if(!empty($response)){
            $this->response = $response; 
        }

        if(!empty($this->response)){  
            if(array_key_exists('result', json_decode($this->response->content(),true))){
                $this->resultResponse = json_decode($this->response->content(),true)['result'];
            }          
        }
        
    }

    protected function setResultResponseSimple($response){

        if(!empty($response)){
            $this->resultResponse = $response; 
        }        
    }

    protected function checkJSONResponse($response){

        // Verify status 200 in response
        $response->assertStatus($this->assertStatus);
                
        // Verify structure in response
        $response->assertJsonStructure($this->assertJsonStructure);

        // Verify values in response
        foreach ($this->assertJson as $assertJson) {
            $response->assertJson($assertJson);
        }
    }

    protected function checkRecordInDatabase($model,$modelFactory,$databaseHas){
        $modelDB = new $model;

        if(empty($this->resultResponse)) return;
        if(empty($this->fieldsDatabaseHas)) return;

        $fields = [];
        foreach ($this->fieldsDatabaseHas as $fieldDatabaseHas) {
            $fields[$fieldDatabaseHas] = $this->resultResponse[$fieldDatabaseHas];
        }

        if($databaseHas){
            $this->assertDatabaseHas($modelDB->getTable(), $fields);
        }else{
            $this->assertDatabaseMissing($modelDB->getTable(), $fields);
        }
    }

    protected function checkRecordInDB(){
        $model = $this->baseModel;
        $modelFactory = "";
        $databaseHas = $this->databaseHas;

        $this->checkRecordInDatabase($model,$modelFactory,$databaseHas);
    }

    protected function getAPIToken(){
        if(is_null(auth()->user()))
            return null; 
        return auth()->user()->api_token;
    }

    protected function get_api_token()
    {
        // Generate an user object
        // $user = factory(User::class)->create(['country_code' => '55']);
        // $store = factory(Store::class)->create(['country_id' => 1 ]);
        $user = factory(User::class)->create(['store_id' => 1]);
        $user->password = 'secret';
        $user->country_code = '55';  
        
        //Submit post request to create an user endpoint
        $response = $this->post('api/login', $user->toArray());

        $this->actingAs($user);
        
        //Verify in the database
        $this->assertNotEmpty(json_decode($response->content(),true)['result']['api_token']);

        auth()->user()->api_token = json_decode($response->content(),true)['result']['api_token'];
        //return json_decode($response->content(),true)['result'];

        return $this;
    }

    protected function read($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        //Submit post request with autorizathion header
        $this->response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->get($route);
 
        $this->checkJSONResponse($this->response);
            
        return $this->response;
    } 

    protected function create($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryMake($model, $attributes);

        //Submit post request with autorizathion header
        $this->response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->post($route, $modelFactory->toArray());
        
        $this->checkJSONResponse($this->response);

        $this->setResultResponse();
        $this->checkRecordInDatabase($model,$modelFactory, $this->databaseHas);
            
        return $this->response;
    }

    protected function readBy($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryCreate($model, $attributes);

        //Submit post request with autorizathion header
        $this->response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->get($route . $modelFactory->id);

        $this->checkJSONResponse($this->response);
            
        $this->setResultResponse();
        $this->checkRecordInDatabase($model,$modelFactory, $this->databaseHas);

        return $this->response;
    }

    protected function update($attributes = [], $attributesMandatory = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryCreate($model, $attributesMandatory);

        // Verify in the db
        $this->setResultResponseSimple($modelFactory->toArray());
        $this->checkRecordInDatabase($model,$modelFactory,true);
        $this->resultResponse = null;

        $modelFactoryNew = $this->factoryMake($model, $attributes);

        //Assign values to update
        $model_keys = array_keys($modelFactoryNew->toArray());
        foreach($model_keys as $key) {
            $modelFactory[$key] = $modelFactoryNew[$key];
        }

        //Submit post request with autorizathion header
        $this->response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->put($route . $modelFactory->id , $modelFactory->toArray());
    
        $this->checkJSONResponse($this->response);

        $this->setResultResponse();
        $this->checkRecordInDatabase($model,$modelFactory,$this->databaseHas);

        return $this->response;
    }

    protected function destroy($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryCreate($model, $attributes);

        //Submit post request with autorizathion header
        $this->response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                         ->delete($route . $modelFactory->id);

        $this->checkJSONResponse($this->response);
        
        $this->setResultResponse();
        $this->checkRecordInDatabase($model,$modelFactory,$this->databaseHas);
        
        return $this->response;
    }

    protected function softDestroy($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryCreate($model, $attributes);

        //Submit post request with autorizathion header
        $this->response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                         ->delete($route . $modelFactory->id);

        $this->checkJSONResponse($this->response);
         
        //Check softDelete
        $modelDB = new $model;
        $this->assertSoftDeleted($modelDB->getTable(), $modelFactory->toArray());
        
        return $this->response;
    }


    protected function checkOptionCRUD($option){
        !in_array($option, ['C','U']) ?  $this->assertEquals('OK','Unknown option.') : null;
    }

    // private function factoryCreate($class, $attributes = [], $times = null)
    // {
    //     return factory($class, $times)->create($attributes);
    // }

    // private function factoryMake($class, $attributes = [], $times = null)
    // {
    //     return factory($class, $times)->make($attributes);
    // }

    // private function factoryRaw($class, $attributes = [], $times = null)
    // {
    //     return factory($class, $times)->raw($attributes);
    // }
}
