<?php

namespace Tests\Unit;

use App\Item;
use App\User;
use App\Price;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Http\Controllers\API\ItemController;

class ItemTest extends TestCase
{ 
    use RefreshDatabase;
    //use DatabaseMigrations;

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

    public function test_item_unauthenticated_user()
    {       
        //Submit post request with autorizathion header
        $response = $this->get('api/items');
        
        // Verify status 200 
        $response->assertStatus(401);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Unauthenticated.']);
         
    }

    //TEST FUNCTION index

    public function test_item_get_all()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->get('api/items');
        
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
          ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Items retrieved successfully.']);
         
    }

    //TEST FUNCTION create/store

    public function test_item_create_name_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->make(['name' => '']);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/items', $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name field is required.']);
         
    }

    public function test_item_create_store_id_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->make(['store_id' => '']);

         // Generate a price object
        $price = factory(Price::class)->make();

        $item->price = $price->price;

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/items', $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The store id field is required.']);
         
    }

    public function test_item_create_price_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->make(['price' => '']);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/items', $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The price field is required.']);
         
    }

    public function test_item_create_price_must_be_a_number()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->make(['price' => 'abc']);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/items', $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The price must be a number.']);
         
    }

    public function test_item_create_price_must_be_a_positive_number()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->make(['price' => -4]);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/items', $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The price must be between 0.00 and 99999.99.']);
         
    }

    public function test_item_create_ok()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->make();

        // Generate a price object
        // $price = factory(Price::class)->make(['price' => $item->price,
        //                                       'item_id' => $item->id]);
       
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/items', $item->toArray());

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        //$this->assertDatabaseHas('prices', $price->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        //Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
            ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Items created successfully.']);
         
    }

    //TEST FUNCTION show

    public function test_item_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->get('api/items/' . '0');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Item not found.']);
         
    }

    public function test_item_show_by_id()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->create();

        // Generate a price object
        $price = factory(Price::class)->create(['price' => $item->price,
                                              'item_id' => $item->id]);
       
        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
             ->get('api/items/' . $item->id);
        
        // Verify status 200 
        $response->assertStatus(200);

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('prices', $price->toArray());
 
        
        //Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
            ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Item retrieved successfully.']);
         
    }

    //TEST FUNCTION update

    public function test_item_update_name_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->create();

        // Generate a price object
        $price = factory(Price::class)->create(['price' => $item->price,
                                              'item_id' => $item->id]);

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('prices', $price->toArray());

        // Set values to Update
        $item->name = '';

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->put('api/items/' . $item->id ,  $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name field is required.']);
         
    }

    public function test_item_update_name()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->create();

        // Generate a price object
        $price = factory(Price::class)->create(['price' => $item->price,
                                              'item_id' => $item->id]);

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('prices', $price->toArray());

        // Set values to Update
        $newItem = factory(Item::class)->make();

        $item->name = $newItem->name;

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->put('api/items/' . $item->id ,  $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Item updated successfully.']);
         
    }

    public function test_item_update_price()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a item object
        $item = factory(Item::class)->create();

        // Generate a price object
        $price = factory(Price::class)->create(['price' => $item->price,
                                                'item_id' => $item->id]);

        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('prices', $price->toArray());

        // Set values to Update
        $newItem = factory(Item::class)->make();

        $item->price = $newItem->price;

         // Generate a price object
        $price = factory(Price::class)->make(['price' => $item->price,
                                              'item_id' => $item->id]);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->put('api/items/' . $item->id ,  $item->toArray());
        
        //Verify in the database
        $this->assertDatabaseHas('items', $item->toArray());
        $this->assertDatabaseHas('prices', $price->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Item updated successfully.']);
         
    }

    //TEST FUNCTION delete

    public function test_item_delete_id_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/items/' . '0');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Item not found.']);
         
    }

    public function test_item_delete()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a item 
        $item = factory(Item::class)->create();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/items/' . $item->id);
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Item deleted successfully.']);
         
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
}
