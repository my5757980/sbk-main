<?php

namespace App\Http\Controllers\Admin;

use App\Models\VehicleBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $data = VehicleBrand::orderBy('sort_order')->orderByDesc('id')->get();

        return view('admin.vehicle-brands.index', compact('data'));
    }

    public function create()
    {
        $data = new VehicleBrand(['status' => 'published']);
        $form = [
            'heading' => 'Create Brand',
            'method' => 'POST',
            'action' => route('admin.vehicle-brand.store'),
            'cancel_url' => route('admin.vehicle-brand.index'),
        ];

        return view('admin.vehicle-brands.form', compact('data', 'form'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->uploadFile($request->file('logo'), 'brand');
        }

        VehicleBrand::create($validated);

        return redirect()->route('admin.vehicle-brand.index')->with('success', 'Brand has been added successfully.');
    }

    public function edit($id)
    {
        $data = VehicleBrand::findOrFail($id);
        $form = [
            'heading' => 'Edit Brand',
            'method' => 'PUT',
            'action' => route('admin.vehicle-brand.update', $id),
            'cancel_url' => route('admin.vehicle-brand.index'),
        ];

        return view('admin.vehicle-brands.form', compact('data', 'form'));
    }

    public function update(Request $request, $id)
    {
        $brand = VehicleBrand::findOrFail($id);
        $validated = $this->validatedData($request, $id);
        $validated['slug'] = $this->uniqueSlug($validated['name'], $brand->slug);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->updateFile($request->file('logo'), $brand->logo, 'brand');
        }

        $brand->update($validated);

        return redirect()->route('admin.vehicle-brand.index')->with('success', 'Brand has been updated successfully.');
    }

    public function destroy($id)
    {
        $brand = VehicleBrand::findOrFail($id);

        if ($brand->products()->exists()) {
            return back()->with('error', 'This brand is attached to products and cannot be deleted.');
        }

        DB::transaction(function () use ($brand) {
            if ($brand->logo && file_exists(uploadsDir('front') . $brand->logo)) {
                unlink(uploadsDir('front') . $brand->logo);
            }

            $brand->delete();
        });

        return redirect()->route('admin.vehicle-brand.index')->with('success', 'Brand has been deleted successfully.');
    }

    private function validatedData(Request $request, $id = null)
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:192'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:published,draft'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueSlug($name, $currentSlug = null)
    {
        $slug = Str::slug($name);
        $query = VehicleBrand::where('slug', $slug);

        if ($currentSlug) {
            $query->where('slug', '!=', $currentSlug);
        }

        if (!$query->exists()) {
            return $slug;
        }

        return $slug . '-' . (VehicleBrand::where('slug', 'like', $slug . '%')->count() + 1);
    }
}
