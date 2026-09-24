<?php

namespace App\Http\Controllers\Admin;

use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleModelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $data = VehicleModel::with('brand')->orderByDesc('id')->get();

        return view('admin.vehicle-models.index', compact('data'));
    }

    public function create()
    {
        $data = new VehicleModel(['status' => 'published']);
        $brands = VehicleBrand::orderBy('name')->get();
        $form = [
            'heading' => 'Create Model',
            'method' => 'POST',
            'action' => route('admin.vehicle-model.store'),
            'cancel_url' => route('admin.vehicle-model.index'),
        ];

        return view('admin.vehicle-models.form', compact('data', 'brands', 'form'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['name'], $validated['vehicle_brand_id']);

        VehicleModel::create($validated);

        return redirect()->route('admin.vehicle-model.index')->with('success', 'Model has been added successfully.');
    }

    public function edit($id)
    {
        $data = VehicleModel::findOrFail($id);
        $brands = VehicleBrand::orderBy('name')->get();
        $form = [
            'heading' => 'Edit Model',
            'method' => 'PUT',
            'action' => route('admin.vehicle-model.update', $id),
            'cancel_url' => route('admin.vehicle-model.index'),
        ];

        return view('admin.vehicle-models.form', compact('data', 'brands', 'form'));
    }

    public function update(Request $request, $id)
    {
        $model = VehicleModel::findOrFail($id);
        $validated = $this->validatedData($request);
        $validated['slug'] = $this->uniqueSlug($validated['name'], $validated['vehicle_brand_id'], $model->id);

        $model->update($validated);

        return redirect()->route('admin.vehicle-model.index')->with('success', 'Model has been updated successfully.');
    }

    public function destroy($id)
    {
        $model = VehicleModel::findOrFail($id);

        if ($model->products()->exists()) {
            return back()->with('error', 'This model is attached to products and cannot be deleted.');
        }

        $model->delete();

        return redirect()->route('admin.vehicle-model.index')->with('success', 'Model has been deleted successfully.');
    }

    private function validatedData(Request $request)
    {
        return $request->validate([
            'vehicle_brand_id' => ['required', 'exists:vehicle_brands,id'],
            'name' => ['required', 'string', 'max:192'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:published,draft'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueSlug($name, $brandId, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $query = VehicleModel::where('vehicle_brand_id', $brandId)->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if (!$query->exists()) {
            return $slug;
        }

        return $slug . '-' . (VehicleModel::where('vehicle_brand_id', $brandId)->where('slug', 'like', $slug . '%')->count() + 1);
    }
}
