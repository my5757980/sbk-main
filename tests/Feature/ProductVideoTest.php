<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckAdminPermission;
use App\Models\Product;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductVideoTest extends TestCase
{
    use DatabaseTransactions;

    private string $originalDirectory;
    private string $videoTestDirectory;
    private array $fields;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([Authenticate::class, CheckAdminPermission::class]);
        $this->originalDirectory = getcwd();
        $this->videoTestDirectory = storage_path('framework/testing/product-video-' . Str::uuid());
        File::makeDirectory($this->videoTestDirectory, 0755, true);
        $this->app->usePublicPath($this->videoTestDirectory);
        chdir($this->videoTestDirectory);
        $brand = VehicleBrand::create(['name' => 'Video Test', 'slug' => 'video-test-' . Str::uuid(), 'status' => 'published', 'sort_order' => 0]);
        $model = VehicleModel::create(['vehicle_brand_id' => $brand->id, 'name' => 'Test', 'slug' => 'video-model-' . Str::uuid(), 'status' => 'published', 'sort_order' => 0]);
        $this->fields = [
            'vehicle_brand_id' => $brand->id, 'vehicle_model_id' => $model->id,
            'title' => 'Video Test ' . Str::uuid(), 'year' => 2026, 'price' => 57500,
            'mileage' => 0, 'chassis_no' => 'VIDEO-TEST', 'dimension' => '1 x 1 x 1 m',
            'm3' => '1', 'steering' => 'Right Hand Drive', 'driving_type' => '4WD', 'status' => 'published',
            'meta_keywords' => 'video test',
        ];
    }

    protected function tearDown(): void
    {
        if (isset($this->originalDirectory)) {
            chdir($this->originalDirectory);
            File::deleteDirectory($this->videoTestDirectory);
        }
        parent::tearDown();
    }

    private function createProduct(?UploadedFile $video = null): Product
    {
        $fields = $this->fields;
        $fields['images'] = [UploadedFile::fake()->image('car.jpg')];
        if ($video) {
            $fields['video'] = $video;
        }
        $response = $this->post(route('admin.product.store'), $fields)->assertSessionHasNoErrors();
        $product = Product::where('title', $this->fields['title'])->firstOrFail();
        $response->assertRedirect(route('admin.product.index'));

        return $product;
    }

    private function updateFields(Product $product, array $extra = []): array
    {
        return array_merge($this->fields, ['existing_images' => $product->images], $extra);
    }

    public function test_product_without_video_has_no_video_section(): void
    {
        $product = $this->createProduct();
        $this->assertNull($product->video);
        $this->get(route('product.show', $product))->assertOk()->assertDontSee('sbk-video-section');
    }

    public function test_uploaded_video_is_stored_and_shown_on_the_product_detail_page(): void
    {
        $product = $this->createProduct(UploadedFile::fake()->create('car.mp4', 128, 'video/mp4'));
        $this->assertStringStartsWith('product-video-', $product->video);
        $this->assertStringEndsWith('.mp4', $product->video);
        $this->assertFileExists(public_path(uploadsDir('front') . $product->video));
        $this->get(route('product.show', $product))->assertOk()
            ->assertSee('sbk-video-section')->assertSee($product->video)->assertSee('controls playsinline', false);
    }

    public function test_editing_preserves_the_video_and_replacing_or_removing_it_cleans_up_files(): void
    {
        $product = $this->createProduct(UploadedFile::fake()->create('first.mp4', 128, 'video/mp4'));
        $first = $product->video;
        $this->put(route('admin.product.update', $product), $this->updateFields($product))
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.product.index'));
        $this->assertSame($first, $product->fresh()->video);
        $this->assertFileExists(public_path(uploadsDir('front') . $first));

        $this->put(route('admin.product.update', $product), $this->updateFields($product, [
            'video' => UploadedFile::fake()->create('second.webm', 128, 'video/webm'),
        ]))->assertSessionHasNoErrors()->assertRedirect(route('admin.product.index'));
        $second = $product->fresh()->video;
        $this->assertNotSame($first, $second);
        $this->assertFileDoesNotExist(public_path(uploadsDir('front') . $first));
        $this->assertFileExists(public_path(uploadsDir('front') . $second));

        $this->put(route('admin.product.update', $product), $this->updateFields($product, ['remove_video' => '1']))
            ->assertSessionHasNoErrors();
        $this->assertNull($product->fresh()->video);
        $this->assertFileDoesNotExist(public_path(uploadsDir('front') . $second));
        $this->get(route('product.show', $product))->assertOk()->assertDontSee('sbk-video-section');
    }

    public function test_deleting_a_product_removes_its_video(): void
    {
        $product = $this->createProduct(UploadedFile::fake()->create('car.ogv', 128, 'video/ogg'));
        $path = public_path(uploadsDir('front') . $product->video);
        $this->assertFileExists($path);
        $this->delete(route('admin.product.destroy', $product))->assertRedirect(route('admin.product.index'));
        $this->assertFileDoesNotExist($path);
        $this->assertNull(Product::find($product->id));
    }

    public function test_invalid_or_oversized_uploads_do_not_replace_the_existing_video(): void
    {
        $product = $this->createProduct(UploadedFile::fake()->create('car.mp4', 128, 'video/mp4'));
        $video = $product->video;
        foreach ([
            UploadedFile::fake()->create('script.php', 1, 'application/x-php'),
            UploadedFile::fake()->create('too-large.mp4', 102401, 'video/mp4'),
        ] as $file) {
            $this->putJson(route('admin.product.update', $product), $this->updateFields($product, ['video' => $file]))
                ->assertUnprocessable()->assertJsonValidationErrors('video');
            $this->assertSame($video, $product->fresh()->video);
            $this->assertFileExists(public_path(uploadsDir('front') . $video));
        }
    }
}
