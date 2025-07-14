<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;


class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $jwtToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CategorySeeder::class, ProductSeeder::class, RoleSeeder::class]);
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        auth()->login($this->user);
        Storage::fake('public');
    }

    /**
     * A basic feature test example.
     */
    public function test_product_index(): void
    {
        $response = $this->getJson('api/products');
        $response->assertStatus(200);
        $this->assertDatabaseCount('products', 30);
    }

    public function test_product_show_success(): void
    {
        $product = Product::first();
        $response = $this->getJson("api/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
        ]);
    }

    public function test_product_show_fail(): void
    {
        $product = Product::max('id') + 1;
        $response = $this->getJson("api/products/{$product}");
        $response->assertStatus(404);
    }

    public function test_product_destroy_with_admin_role_success()
    {
        $this->withoutExceptionHandling();
        $product = Product::first();
        $response = $this->deleteJson("api/admin/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_destroy_with_admin_role_fail(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(ModelNotFoundException::class);

        $productNotExist = Product::max('id') + 1;
        $response = $this->deleteJson("api/admin/products/{$productNotExist}");

        $response->assertStatus(404);
    }

    public function test_product_destroy_without_admin_role_fail(): void
    {
        $product = Product::first();
        $user = User::factory()->create();
        auth()->login($user);
        $response = $this->deleteJson("api/admin/products/{$product->id}");

        $response->assertStatus(403);
    }

    public function test_store_product_with_admin_role_success(): void
    {
        $data = [
            'name' => 'test name',
            'description' => 'test description',
            'price' => 500,
            'category_id' => Category::first()->id,
            'is_available' => true,
        ];
        $response = $this->postJson("api/admin/products", $data);

        $response->assertOk();
        $this->assertDatabaseHas('products', $data);
    }

    public function test_store_product_without_admin_role_fail(): void
    {
        $user = User::factory()->create();
        auth()->login($user);
        $data = [
            'name' => 'test name',
            'description' => 'test description',
            'price' => 500,
            'category_id' => Category::first()->id,
            'is_available' => true,
        ];
        $response = $this->postJson("api/admin/products", $data);

        $response->assertStatus(403);
    }

    public function test_store_product_without_name_fail(): void
    {
        $data = [
            'name' => '',
            'description' => 'test description',
            'price' => 500,
            'category_id' => Category::first()->id,
            'is_available' => true,
        ];
        $response = $this->postJson("api/admin/products", $data);

        $response->assertStatus(422);
    }

    public function test_store_product_with_image_success(): void
    {
        $image = UploadedFile::fake()->image('test.jpg');
        $category = Category::first();
        $data = [
            'name' => 'test name',
            'description' => 'test description',
            'price' => 500,
            'category_id' => $category->id,
            'is_available' => true,
            'image' => $image,
        ];
        $response = $this->postJson("api/admin/products", $data);
        $response->assertOk();

        $categoryFolder = Str::slug($category->name);
        Storage::disk('public')->assertExists("images/{$categoryFolder}/" . $image->hashName());
    }

    public function test_update_product_with_admin_role_success(): void
    {
        $product = Product::first();
        $data = [
            'name' => 'test name',
            'description' => 'test description',
            'price' => 500,
        ];
        $response = $this->putJson("api/admin/products/{$product->id}", $data);

        $response->assertOk();
        $this->assertDatabaseHas('products', $data);
        $this->assertNotEquals($product->name, $data['name']);
        $this->assertNotEquals($product->description, $data['description']);
        $this->assertNotEquals($product->price, $data['price']);
    }

    public function test_update_product_without_name_fail(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(ModelNotFoundException::class);
        $productNotExist = Product::max('id') + 1;
        $data = [
            'name' => 'test name',
            'description' => 'test description',
            'price' => 500,
        ];
        $response = $this->putJson("api/admin/products/{$productNotExist}", $data);

        $response->assertStatus(403);
    }

    public function test_update_product_without_admin_role_fail(): void
    {
        $user = User::factory()->create();
        auth()->login($user);
        $product = Product::first();
        $data = [
            'name' => 'test name',
            'description' => 'test description',
            'price' => 500,
        ];
        $response = $this->putJson("api/admin/products/{$product->id}", $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', $data);
    }

}
