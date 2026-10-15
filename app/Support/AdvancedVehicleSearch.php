<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdvancedVehicleSearch
{
    public const FIELDS = [
        'fuel_type' => ['Fuel Type', 'fuel_type'],
        'engine' => ['Engine', 'engine'],
        'color' => ['Color', 'color'],
        'driving_type' => ['Driving Type', 'driving_type'],
        'location' => ['Location', 'location'],
        'dimension' => ['Dimensions', 'dimension'],
        'm3' => ['M3', 'm3'],
        'transmission_detail' => ['Transmission Detail', 'transmission'],
        'ref_no' => ['Reference No.', 'ref_no'],
        'stock_no' => ['Stock No.', 'stock_no'],
        'chassis_no' => ['Chassis No.', 'chassis_no'],
    ];

    public static function normalize(string $value): string
    {
        return Str::lower(trim($value));
    }

    public static function options(Collection $products): array
    {
        $fields = [];
        foreach (self::FIELDS as $parameter => [$label, $column]) {
            $fields[$parameter] = [
                'label' => $label,
                'values' => $products->pluck($column)->filter(fn ($value) => trim((string) $value) !== '')
                    ->map(fn ($value) => trim((string) $value))->unique()->sort()->values(),
            ];
        }

        $specifications = [];
        foreach ($products as $product) {
            foreach (($product->vehicle_specifications ?: []) as $spec) {
                $name = trim((string) ($spec['name'] ?? ''));
                $value = trim((string) ($spec['value'] ?? ''));
                if ($name === '' || $value === '' || self::normalize($name) === 'grade') {
                    continue;
                }
                $key = sha1(self::normalize($name));
                $specifications[$key]['label'] = $specifications[$key]['label'] ?? $name;
                $specifications[$key]['values'][] = $value;
            }
        }
        foreach ($specifications as &$specification) {
            $specification['values'] = collect($specification['values'])->unique()->sort()->values();
        }
        unset($specification);
        uasort($specifications, fn ($a, $b) => strnatcasecmp($a['label'], $b['label']));

        return compact('fields', 'specifications');
    }

    public static function parameters(Request $request): array
    {
        $parameters = [];
        foreach (self::FIELDS as $parameter => $_) {
            $value = $request->query($parameter);
            if (is_string($value) && trim($value) !== '') {
                $parameters[$parameter] = trim($value);
            }
        }
        $specifications = $request->query('specs', []);
        foreach (is_array($specifications) ? $specifications : [] as $key => $value) {
            if (is_string($key) && preg_match('/^[a-f0-9]{40}$/', $key) && is_string($value) && trim($value) !== '') {
                $parameters['specs'][$key] = trim($value);
            }
        }

        return $parameters;
    }

    public static function filter(Collection $products, array $parameters): Collection
    {
        foreach (self::FIELDS as $parameter => [$_, $column]) {
            if (isset($parameters[$parameter])) {
                $value = self::normalize($parameters[$parameter]);
                $products = $products->filter(fn ($product) => self::normalize((string) $product->{$column}) === $value);
            }
        }
        foreach (($parameters['specs'] ?? []) as $key => $value) {
            $products = $products->filter(function ($product) use ($key, $value) {
                foreach (($product->vehicle_specifications ?: []) as $spec) {
                    if (sha1(self::normalize((string) ($spec['name'] ?? ''))) === $key
                        && self::normalize((string) ($spec['value'] ?? '')) === self::normalize($value)) {
                        return true;
                    }
                }

                return false;
            });
        }

        return $products;
    }
}
