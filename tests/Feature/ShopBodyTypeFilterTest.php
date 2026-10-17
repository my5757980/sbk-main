<?php
namespace Tests\Feature;

use App\Models\Product;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopBodyTypeFilterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_body_types_are_all_available_and_filter_individually(): void
    {
        $token = 'body-filter-' . Str::uuid();
        $brand = VehicleBrand::create(['name' => $token, 'slug' => $token, 'status' => 'published', 'sort_order' => 0]);
        $model = VehicleModel::create(['vehicle_brand_id' => $brand->id, 'name' => $token, 'slug' => $token, 'status' => 'published', 'sort_order' => 0]);
        $ids = [];
        foreach (Product::BODY_TYPES as $index => $type) {
            $product = Product::create([
                'vehicle_brand_id' => $brand->id, 'vehicle_model_id' => $model->id,
                'title' => $token . ' ' . $type, 'slug' => $token . '-' . $index,
                'body_type' => $type, 'year' => 2026, 'price' => 10000 + $index,
                'mileage' => 0, 'chassis_no' => $token, 'dimension' => '1 x 1 x 1 m',
                'm3' => '1', 'steering' => 'Left Hand Drive', 'driving_type' => '2WD',
                'status' => 'published', 'images' => [],
            ]);
            $ids[$type] = $product->id;
        }

        $this->get('/shop?' . http_build_query(['search' => 'no-matching-' . $token]))
            ->assertOk()
            ->assertViewHas('types', fn ($types) => collect(Product::BODY_TYPES)->diff($types)->isEmpty())
            ->assertViewHas('products', fn ($products) => $products->total() === 0);

        foreach ($ids as $type => $id) {
            $this->get('/shop?' . http_build_query(['search' => $token, 'types' => [$type]]))
                ->assertOk()
                ->assertViewHas('selectedTypes', [$type])
                ->assertViewHas('products', fn ($products) => $products->total() === 1 && $products->first()->id === $id);
        }

        $this->get('/shop?' . http_build_query(['search' => $token, 'types' => ['Van', 'Pickup Truck']]))
            ->assertOk()
            ->assertViewHas('products', fn ($products) => $products->total() === 2 && $products->pluck('id')->sort()->values()->all() === collect([$ids['Van'], $ids['Pickup Truck']])->sort()->values()->all());

        $this->get('/shop?' . http_build_query(['search' => $token, 'types' => ['Van / MPV']]))
            ->assertOk()
            ->assertViewHas('products', fn ($products) => $products->total() === 3);
    }
}
