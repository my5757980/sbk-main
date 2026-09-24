<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function __construct(private Service $service)
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $data = $this->service->allService();

        return view('admin.services.index', compact('data'));
    }

    public function create()
    {
        $data = new Service(['status' => 'published']);
        $form = [
            'heading' => 'Create Service',
            'method' => 'POST',
            'action' => route('admin.service.store'),
            'cancel_url' => route('admin.service.index'),
        ];

        return view('admin.services.form', compact('data', 'form'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = generateServiceUniqueSlug($request->slug ?: $request->title);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile($request->file('image'), 'service');
        }

        $this->service->storeService($data);

        return redirect()->route('admin.service.index')->with('success', 'Service has been added successfully.');
    }

    public function edit($id)
    {
        $data = $this->service->findService($id);
        $form = [
            'heading' => 'Edit Service',
            'method' => 'PUT',
            'action' => route('admin.service.update', $id),
            'cancel_url' => route('admin.service.index'),
        ];

        return view('admin.services.form', compact('data', 'form'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $data = $this->validatedData($request);
        $data['slug'] = generateServiceUniqueSlug($request->slug ?: $request->title, $service->slug);

        if ($request->hasFile('image')) {
            $data['image'] = $this->updateFile($request->file('image'), $request->previous_image, 'service');
        } else {
            $data['image'] = $request->previous_image;
        }

        $service->update($data);

        return redirect()->route('admin.service.index')->with('success', 'Service has been updated successfully.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        DB::transaction(function () use ($service) {
            if ($service->image && file_exists(uploadsDir('front') . $service->image)) {
                unlink(uploadsDir('front') . $service->image);
            }

            $service->delete();
        });

        return redirect()->route('admin.service.index')->with('success', 'Service has been deleted successfully.');
    }

    private function validatedData(Request $request)
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:192'],
            'menu_title' => ['nullable', 'string', 'max:192'],
            'slug' => ['required', 'string', 'max:192'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'primary_cta_label' => ['nullable', 'string', 'max:192'],
            'secondary_cta_label' => ['nullable', 'string', 'max:192'],
            'image' => ['nullable', 'image', 'max:4096'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:192'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:published,draft'],
        ]);
    }
}
