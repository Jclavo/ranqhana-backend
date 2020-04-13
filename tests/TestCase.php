<?php

namespace Tests;

use App\User;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\TestCaseBase;
use Faker;

abstract class TestCase extends TestCaseBase
{
    use CreatesApplication;

    protected $baseRoute = null;
    protected $baseModel = null;
    protected $assertStatus = 200;
    protected $assertJsonStructure = [];
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

        if($databaseHas){
            $this->assertDatabaseHas($modelDB->getTable(), $modelFactory->toArray());
        }else{
            $this->assertDatabaseMissing($modelDB->getTable(), $modelFactory->toArray());
        }
    }

    protected function getAPIToken(){
        if(is_null(auth()->user()))
            return null; 
        return auth()->user()->api_token;
    }

    protected function get_api_token()
    {
        // Generate an user object
        $user = factory(User::class)->create(['country_code' => '55']);
                
        $user->password = 'secret';
        
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
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->get($route);
 
        $this->checkJSONResponse($response);
            
        return $response;
    } 

    protected function create($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryMake($model, $attributes);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->post($route, $modelFactory->toArray());
        
        $this->checkJSONResponse($response);

        $this->checkRecordInDatabase($model,$modelFactory, $this->databaseHas);
            
        return $response;
    }

    protected function readBy($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryCreate($model, $attributes);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->get($route . $modelFactory->id);

        $this->checkJSONResponse($response);
            
        $this->checkRecordInDatabase($model,$modelFactory, $this->databaseHas);

        return $response;
    }

    protected function update($attributes = [], $attributesMandatory = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryCreate($model, $attributesMandatory);

        // Verify in the db
        $this->checkRecordInDatabase($model,$modelFactory,true);

        $modelFactoryNew = $this->factoryMake($model, $attributes);

        //Assign values to update
        $model_keys = array_keys($modelFactoryNew->toArray());
        foreach($model_keys as $key) {
            $modelFactory[$key] = $modelFactoryNew[$key];
        }

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                        ->put($route . $modelFactory->id , $modelFactory->toArray());
    
        $this->checkJSONResponse($response);

        $this->checkRecordInDatabase($model,$modelFactory,$this->databaseHas);

        return $response;
    }

    protected function destroy($attributes = [], $model = '', $route = '')
    {
        $route = $this->baseRoute ?? $route;
        $model = $this->baseModel ?? $model;

        $modelFactory = $this->factoryCreate($model, $attributes);

        //Submit post request with autorizathion header
        $response = $this->withHeaders(['Authorization' => 'Bearer '. $this->getAPIToken()])
                         ->delete($route . $modelFactory->id);

        $this->checkJSONResponse($response);
            
        $this->checkRecordInDatabase($model,$modelFactory,$this->databaseHas);
        
        return $response;
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
