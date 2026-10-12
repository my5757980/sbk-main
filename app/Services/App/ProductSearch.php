<?php

namespace App\Services\App;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The shop's search, filters and sort for the SBK app (routes/app.php).
 *
 * The same rules as SiteController@shop, line for line - the app and the
 * website must find the same vehicles for the same choices. SiteController is
 * left as it is; if its rules change, change them here too.
 */
class ProductSearch
{
    /** Every published vehicle, in the shop's own order. */
    public function all(): Collection
    {
        return Product::with(['brand', 'vehicleModel'])
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
    }

    /** SiteController::vehicleTypeLabel - free-typed body types grouped for the filter. */
    public static function typeLabel(?string $type): ?string
    {
        $type = trim((string) $type);
        if ($type === '') {
            return null;
        }
        $normalized = Str::lower($type);

        return match (true) {
            Str::contains($normalized, 'sedan') => 'Sedan',
            Str::contains($normalized, 'suv') => 'SUV',
            Str::contains($normalized, 'hatchback') => 'Hatchback',
            Str::contains($normalized, 'wagon') => 'Wagon',
            Str::contains($normalized, 'van') || Str::contains($normalized, 'mpv') => 'Van / MPV',
            Str::contains($normalized, 'coupe') => 'Coupe',
            Str::contains($normalized, 'roadster') || Str::contains($normalized, 'convertible') => 'Convertible / Roadster',
            Str::contains($normalized, 'bus') => 'Bus',
            Str::contains($normalized, 'truck') || Str::contains($normalized, 'double cab') => 'Truck / Double Cab',
            default => 'Other',
        };
    }

    /** SiteController::productGrade - the specification named "grade". */
    public static function grade(Product $product): ?string
    {
        foreach (($product->vehicle_specifications ?: []) as $specification) {
            if (Str::lower(trim((string) ($specification['name'] ?? ''))) === 'grade') {
                $grade = trim((string) ($specification['value'] ?? ''));

                return $grade !== '' ? $grade : null;
            }
        }

        return null;
    }

    /**
     * The shop's filters. $f uses the website's own parameter names:
     * search, brands[], model, year, types[], grades[], max_mileage,
     * transmission, min_price, max_price, sort.
     */
    public function filter(Collection $all, array $f): Collection
    {
        $brands = array_values(array_filter((array) ($f['brands'] ?? [])));
        $types = array_values(array_filter((array) ($f['types'] ?? [])));
        $grades = array_values(array_filter((array) ($f['grades'] ?? [])));
        $model = $f['model'] ?? null;
        $year = $f['year'] ?? null;
        $mileage = $f['max_mileage'] ?? null;
        $transmission = $f['transmission'] ?? null;
        $minPrice = $f['min_price'] ?? null;
        $maxPrice = $f['max_price'] ?? null;
        $sort = $f['sort'] ?? 'default';
        $search = trim((string) ($f['search'] ?? ''));

        $found = $all
            ->when($search !== '', function ($collection) use ($search) {
                $needle = Str::lower($search);

                return $collection->filter(function ($product) use ($needle) {
                    return Str::contains(Str::lower($product->title), $needle)
                        || Str::contains(Str::lower((string) $product->stock_no), $needle)
                        || Str::contains(Str::lower((string) $product->chassis_no), $needle)
                        || Str::contains(Str::lower((string) optional($product->brand)->name), $needle)
                        || Str::contains(Str::lower((string) optional($product->vehicleModel)->name), $needle)
                        || Str::contains(Str::lower((string) $product->fuel_type), $needle)
                        || Str::contains(Str::lower((string) $product->body_type), $needle);
                });
            })
            ->when($brands, fn ($c) => $c->whereIn('brand.name', $brands))
            ->when($model, fn ($c) => $c->where('vehicleModel.name', $model))
            ->when($year, fn ($c) => $c->where('year', (int) $year))
            ->when($types, fn ($c) => $c->filter(fn ($p) => in_array(self::typeLabel($p->body_type), $types, true)))
            ->when($grades, fn ($c) => $c->filter(fn ($p) => in_array(self::grade($p), $grades, true)))
            ->when($mileage, fn ($c) => $c->filter(fn ($p) => $p->mileage !== null && $p->mileage !== '' && (int) $p->mileage <= (int) $mileage))
            ->when($transmission, fn ($c) => $c->filter(fn ($p) => Str::contains(Str::lower((string) $p->transmission), Str::lower($transmission))))
            ->when($minPrice !== null && $minPrice !== '', fn ($c) => $c->filter(fn ($p) => (float) $p->price >= (float) $minPrice))
            ->when($maxPrice !== null && $maxPrice !== '', fn ($c) => $c->filter(fn ($p) => (float) $p->price <= (float) $maxPrice));

        return match ($sort) {
            'price-low' => $found->sortBy('price'),
            'price-high' => $found->sortByDesc('price'),
            'year-new' => $found->sortByDesc('year'),
            default => $found,
        };
    }
}
