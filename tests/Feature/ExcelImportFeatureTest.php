<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SpecificationType;
use App\Services\ArticleImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelImportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_import_an_excel_file_following_the_correct_specifications()
    {
        // Arrange
        $fixturePath = base_path('tests/_fixtures/reservdelar_v2.xlsx');
        $service = new ArticleImport($fixturePath);

        // Act
        $service->handleMainProducts();

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Viking Bio G3',
            'slug' => 'viking-bio-g3',
            'main_product' => true,
        ]);
    }

    public function test_it_can_handle_products()
    {
        // Arrange
        $fixturePath = base_path('tests/_fixtures/reservdelar_v2.xlsx');
        $service = new ArticleImport($fixturePath);
        $service->handleSpecifications();
        $service->handleMainProducts();

        // Act
        $service->handleProducts();

        // Assert
        $this->assertDatabaseHas('products', [
            'name' => 'Termostat EGO 55.13212.400',
            'sku' => '120006',
            'slug' => 'termostat-ego-5513212400',
            'main_product' => false,
            'price' => 51000,
        ]);
        $compatibleProduct = Product::where('name', 'XPP 20')->first();
        $product = Product::whereSku('120006')->firstOrFail();
        $this->assertDatabaseHas('compatible_products', [
            'main_product_id' => $compatibleProduct->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_it_can_handle_specifications()
    {
        // Arrange
        $fixturePath = base_path('tests/_fixtures/reservdelar_v2.xlsx');
        $service = new ArticleImport($fixturePath);
        $service->handleMainProducts();

        // Act
        $service->handleSpecifications();

        // Assert
        $this->assertDatabaseHas('specification_types', [
            'name' => 'Spänning (V)',
            'slug' => 'spanning_v',
        ]);
    }

    public function test_it_will_allow_a_main_product_to_have_multiple_compatible_products()
    {
        // Arrange
        $product = Product::factory()->create(['main_product' => false]);
        $mainProducts = Product::factory()->count(5)->create([
            'main_product' => true,
        ]);

        // Act
        $product->compatibleMainProducts()->sync($mainProducts->pluck('id'));

        // Assert
        $this->assertEquals(5, $product->compatibleMainProducts->count());
        $this->assertEquals(1, $mainProducts->first()->compatibleProducts->count());
    }

    public function test_it_will_allow_a_product_to_have_multiple_specifications()
    {
        // Arrange
        $product = Product::factory()->create();
        $specificationType = SpecificationType::factory()->create([
            'name' => 'Spänning (V)',
        ]);

        // Act
        $product->specifications()->sync([
            $specificationType->id => ['content' => '230/400'],
        ]);

        // Assert
        $this->assertEquals(1, $product->specifications->count());
    }

    public function test_it_will_check_if_assets_following_naming_conventions_is_present_and_persist_if_so()
    {
        // Arrange
        $fixturePath = base_path('tests/_fixtures/reservdelar_v2_single.xlsx');
        $service = new ArticleImport($fixturePath);

        // Act
        $service->handleProducts();

        // Assert
        $product = Product::whereSku('110016')->first();
        $this->assertDatabaseHas('media', [
            'model_id' => $product->id,
            'file_name' => '110016_1.png',
        ]);
        $this->assertDatabaseHas('media', [
            'model_id' => $product->id,
            'file_name' => '110016_2.png',
        ]);
    }
}
