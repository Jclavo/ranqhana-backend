<?php

namespace Tests\Unit;

use App\Product;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{ 
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed database
        $this->seed();
       
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

    public function test_product_unauthenticated_user()
    {       
        //Submit post request with autorizathion header
        $response = $this->get('api/products');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Unauthenticated.']);
         
    }

    //TEST FUNCTION index

    public function test_product_get_all()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->get('api/products');
        
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
          ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Products retrieved successfully.']);
         
    }

    //TEST FUNCTION create/store

    public function test_product_create_name_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a product object
        $product = factory(Product::class)->make(['name' => '']);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/products', $product->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('products', $product->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name field is required.']);
         
    }

    public function test_product_create_store_id_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a product object
        $product = factory(Product::class)->make(['store_id' => '']);

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/products', $product->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('products', $product->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The store id field is required.']);
         
    }

    public function test_product_create()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a product object
        $product = factory(Product::class)->make();

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->post('api/products', $product->toArray());
        
        //Verify in the database
        $this->assertDatabaseHas('products', $product->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        //Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
            ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Products created successfully.']);
         
    }

    //TEST FUNCTION show

    public function test_product_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->get('api/products/' . '0');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Product not found.']);
         
    }

    public function test_product_show_by_id()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a product object
        $product = factory(Product::class)->create();

        //Verify in the database
        $this->assertDatabaseHas('products', $product->toArray());

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
             ->get('api/products/' . $product->id);
        
        // Verify status 200 
        $response->assertStatus(200);
        
        //Verify values in response
        $response->assertJsonStructure([
            'status',
            'message',
            'result' => []
            ]);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Product retrieved successfully.']);
         
    }

    //TEST FUNCTION update

    public function test_product_update_name_is_required()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a product object
        $product = factory(Product::class)->create();

        //Verify in the database
        $this->assertDatabaseHas('products', $product->toArray());

        // Set values to Update
        $product->name = '';

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->put('api/products/' . $product->id ,  $product->toArray());
        
        //Verify in the database
        $this->assertDatabaseMissing('products', $product->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'The name field is required.']);
         
    }

    public function test_product_update_name()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
         // Generate a product object
        $product = factory(Product::class)->create();

        //Verify in the database
        $this->assertDatabaseHas('products', $product->toArray());

        // Set values to Update
        $newProduct = factory(Product::class)->make();

        $product->name = $newProduct->name;

        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->put('api/products/' . $product->id ,  $product->toArray());
        
        //Verify in the database
        $this->assertDatabaseHas('products', $product->toArray());

        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Product updated successfully.']);
         
    }

    //TEST FUNCTION update

    public function test_product_delete_id_not_found()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/products/' . '0');
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => false]);
        $response->assertJson(['message' => 'Product not found.']);
         
    }

    public function test_product_delete()
    {
        // get api token from authenticate user
        $api_token = $this->get_api_token();

        // Generate a product 
        $product = factory(Product::class)->create();
        
        //Submit post request with autorizathion header
        $response = 
        
        $this->withHeaders(['Authorization' => 'Bearer '. $api_token])
              ->delete('api/products/' . $product->id);
        
        // Verify status 200 
        $response->assertStatus(200);
        
        // Verify values in response
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => 'Product deleted successfully.']);
         
    }
}
