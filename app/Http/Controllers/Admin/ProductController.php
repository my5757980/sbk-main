<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $data = Product::with(['brand', 'vehicleModel'])->orderByDesc('id')->get();

        return view('admin.products.index', compact('data'));
    }

    public function create()
    {
        $data = new Product(['status' => 'published', 'is_featured' => true]);
        $brands = VehicleBrand::orderBy('name')->get();
        $models = VehicleModel::with('brand')->orderBy('name')->get();
        $homeSectionOptions = Product::HOME_SECTION_OPTIONS;
        $form = [
            'heading' => 'Create Product',
            'method' => 'POST',
            'action' => route('admin.product.store'),
            'cancel_url' => route('admin.product.index'),
        ];

        return view('admin.products.form', compact('data', 'brands', 'models', 'homeSectionOptions', 'form'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        unset($validated['existing_images'], $validated['specification_names'], $validated['specification_values']);
        $this->ensureModelBelongsToBrand($validated['vehicle_model_id'], $validated['vehicle_brand_id']);
        $validated['slug'] = $this->uniqueSlug($validated['title']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['images'] = $this->uploadImages($request);
        $validated['vehicle_specifications'] = $this->formatSpecifications($request);

        Product::create($validated);

        return redirect()->route('admin.product.index')->with('success', 'Product has been added successfully.');
    }

    public function edit($id)
    {
        $data = Product::findOrFail($id);
        $brands = VehicleBrand::orderBy('name')->get();
        $models = VehicleModel::with('brand')->orderBy('name')->get();
        $homeSectionOptions = Product::HOME_SECTION_OPTIONS;
        $form = [
            'heading' => 'Edit Product',
            'method' => 'PUT',
            'action' => route('admin.product.update', $id),
            'cancel_url' => route('admin.product.index'),
        ];

        return view('admin.products.form', compact('data', 'brands', 'models', 'homeSectionOptions', 'form'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $this->validatedData($request, false);
        unset($validated['existing_images'], $validated['specification_names'], $validated['specification_values']);
        $this->ensureModelBelongsToBrand($validated['vehicle_model_id'], $validated['vehicle_brand_id']);
        $validated['slug'] = $this->uniqueSlug($validated['title'], $product->slug);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['vehicle_specifications'] = $this->formatSpecifications($request);

        $existingImages = array_values($request->input('existing_images', []));
        $removedImages = array_diff($product->images ?: [], $existingImages);

        foreach ($removedImages as $image) {
            $this->deleteImage($image);
        }

        $validated['images'] = array_values(array_merge($existingImages, $this->uploadImages($request)));

        $product->update($validated);

        return redirect()->route('admin.product.index')->with('success', 'Product has been updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product) {
            foreach (($product->images ?: []) as $image) {
                $this->deleteImage($image);
            }

            $product->delete();
        });

        return redirect()->route('admin.product.index')->with('success', 'Product has been deleted successfully.');
    }

    private function validatedData(Request $request, $imagesRequired = true)
    {
        return $request->validate([
            'vehicle_brand_id' => ['required', 'exists:vehicle_brands,id'],
            'vehicle_model_id' => ['required', 'exists:vehicle_models,id'],
            'title' => ['required', 'string', 'max:192'],
            'stock_no' => ['nullable', 'string', 'max:100'],
            'chassis_no' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . ((int) date('Y') + 1)],
            'price' => ['required', 'numeric', 'min:0'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'fuel_type' => ['nullable', 'string', 'max:100'],
            'transmission' => ['nullable', 'string', 'max:100'],
            'body_type' => ['nullable', 'string', 'max:100'],
            'engine' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:100'],
            'steering' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:192'],
            'tag' => ['nullable', 'string', 'max:60'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:192'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'specification_names' => ['nullable', 'array'],
            'specification_names.*' => ['nullable', 'string', 'max:120'],
            'specification_values' => ['nullable', 'array'],
            'specification_values.*' => ['nullable', 'string', 'max:255'],
            'images' => [$imagesRequired ? 'required' : 'nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'existing_images' => ['nullable', 'array'],
            'status' => ['required', 'in:published,draft,sold'],
            'home_section' => ['nullable', 'in:' . implode(',', array_keys(Product::HOME_SECTION_OPTIONS))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uploadImages(Request $request)
    {
        $images = [];

        foreach ($request->file('images', []) as $file) {
            $images[] = $this->uploadFile($file, 'product');
        }

        return $images;
    }

    private function formatSpecifications(Request $request)
    {
        $names = $request->input('specification_names', []);
        $values = $request->input('specification_values', []);
        $specifications = [];

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            $value = trim((string) ($values[$index] ?? ''));

            if ($name === '' && $value === '') {
                continue;
            }

            $specifications[] = [
                'name' => $name,
                'value' => $value,
            ];
        }

        return $specifications;
    }

    private function ensureModelBelongsToBrand($modelId, $brandId)
    {
        if (!VehicleModel::where('id', $modelId)->where('vehicle_brand_id', $brandId)->exists()) {
            throw ValidationException::withMessages([
                'vehicle_model_id' => 'The selected model does not belong to the selected brand.',
            ]);
        }
    }

    private function deleteImage($image)
    {
        if ($image && file_exists(uploadsDir('front') . $image)) {
            unlink(uploadsDir('front') . $image);
        }
    }

    private function uniqueSlug($title, $currentSlug = null)
    {
        $slug = Str::slug($title);
        $query = Product::where('slug', $slug);

        if ($currentSlug) {
            $query->where('slug', '!=', $currentSlug);
        }

        if (!$query->exists()) {
            return $slug;
        }

        return $slug . '-' . (Product::where('slug', 'like', $slug . '%')->count() + 1);
    }
}
