@extends('admin.layouts.app')

@section('content')
<section id="number-tabs">
    <div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4 class="card-title">{{ $form['heading'] }}</h4></div>
        <div class="card-content"><div class="card-body">
            <form method="POST" action="{{ $form['action'] }}" enctype="multipart/form-data">
                @csrf
                @method($form['method'])

                <h5 class="mb-1">Basic Vehicle Information</h5>
                <div class="row">
                    <div class="col-md-6"><div class="form-group">
                        <label>Product Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $data->title) }}" required>
                    </div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Brand *</label>
                        <select name="vehicle_brand_id" id="vehicle_brand_id" class="form-control select2" required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {!! matchSelected($brand->id, old('vehicle_brand_id', $data->vehicle_brand_id)) !!}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Model *</label>
                        <select name="vehicle_model_id" id="vehicle_model_id" class="form-control select2" required>
                            <option value="">Select Model</option>
                            @foreach($models as $model)
                                <option value="{{ $model->id }}" data-brand="{{ $model->vehicle_brand_id }}" {!! matchSelected($model->id, old('vehicle_model_id', $data->vehicle_model_id)) !!}>{{ optional($model->brand)->name }} - {{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div></div>
                </div>

                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Ref. No.</label><input type="text" class="form-control" value="{{ $data->ref_no ?: $nextRefNo }}" readonly></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Stock No</label><input type="text" name="stock_no" class="form-control" value="{{ old('stock_no', $data->stock_no) }}"></div></div>
                </div>

                <h5 class="mt-2 mb-1">Specifications</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Year *</label><input type="number" name="year" class="form-control" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('year', $data->year) }}" required></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Price *</label><input type="number" name="price" class="form-control" min="0" step="0.01" value="{{ old('price', $data->price ?? 0) }}" required></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Discounted Price</label><input type="number" name="discounted_price" class="form-control" min="0" step="0.01" value="{{ old('discounted_price', $data->discounted_price) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Mileage *</label><input type="number" name="mileage" class="form-control" min="0" value="{{ old('mileage', $data->mileage) }}" required></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Chassis No *</label><input type="text" name="chassis_no" class="form-control" value="{{ old('chassis_no', $data->chassis_no) }}" required></div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Fuel Type</label>
                        <select name="fuel_type" class="form-control">
                            <option value="">Select Fuel Type</option>
                            @foreach(['Petrol', 'Diesel', 'Electric'] as $fuelType)
                                <option value="{{ $fuelType }}" {!! matchSelected($fuelType, old('fuel_type', $data->fuel_type)) !!}>{{ $fuelType }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Transmission</label>
                        <select name="transmission" class="form-control">
                            <option value="">Select Transmission</option>
                            @foreach(['Automatic Transmission', 'Manual Transmission'] as $transmission)
                                <option value="{{ $transmission }}" {!! matchSelected($transmission, old('transmission', $data->transmission)) !!}>{{ $transmission }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Body Type</label>
                        <select name="body_type" class="form-control">
                            <option value="">Select Body Type</option>
                            @foreach([
                                'Sedan',
                                'Hatchback',
                                'SUV',
                                'Crossover',
                                'MPV / Minivan',
                                'Wagon / Estate',
                                'Coupe',
                                'Convertible / Cabriolet',
                                'Pickup Truck',
                                'Van',
                                'Microvan / Kei Van',
                                'Minibus',
                                'Roadster',
                                'Liftback',
                                'Fastback',
                            ] as $bodyType)
                                <option value="{{ $bodyType }}" {!! matchSelected($bodyType, old('body_type', $data->body_type)) !!}>{{ $bodyType }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group"><label>Engine</label><input type="text" name="engine" class="form-control" value="{{ old('engine', $data->engine) }}" placeholder="2500cc"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Color</label><input type="text" name="color" class="form-control" value="{{ old('color', $data->color) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Dimension *</label><input type="text" name="dimension" class="form-control" value="{{ old('dimension', $data->dimension) }}" placeholder="L x W x H" required></div></div>
                    <div class="col-md-3"><div class="form-group"><label>M3 *</label><input type="text" name="m3" class="form-control" value="{{ old('m3', $data->m3) }}" placeholder="12.34" required></div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Steering *</label>
                        <select name="steering" class="form-control" required>
                            <option value="">Select Steering</option>
                            @foreach(['Right Hand Drive', 'Left Hand Drive'] as $steering)
                                <option value="{{ $steering }}" {!! matchSelected($steering, old('steering', $data->steering)) !!}>{{ $steering }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Driving Type *</label>
                        <select name="driving_type" class="form-control" required>
                            <option value="">Select Driving Type</option>
                            @foreach(['2WD', '4WD'] as $drivingType)
                                <option value="{{ $drivingType }}" {!! matchSelected($drivingType, old('driving_type', $data->driving_type)) !!}>{{ $drivingType }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group"><label>Location</label><input type="text" name="location" class="form-control" value="{{ old('location', $data->location) }}" placeholder="Japan"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Tag</label><input type="text" name="tag" class="form-control" value="{{ old('tag', $data->tag) }}" placeholder="FEATURED"></div></div>
                </div>

                <h5 class="mt-2 mb-1">Vehicle Specifications</h5>
                <div class="row">
                    <div class="col-md-12">
                        <div id="vehicleSpecifications">
                            @php
                                $oldSpecNames = old('specification_names');
                                $oldSpecValues = old('specification_values');
                                $savedSpecs = $data->vehicle_specifications ?: [];
                                $specRows = $oldSpecNames !== null
                                    ? collect($oldSpecNames)->map(fn($name, $index) => ['name' => $name, 'value' => $oldSpecValues[$index] ?? ''])->all()
                                    : ($savedSpecs ?: [['name' => '', 'value' => '']]);
                            @endphp
                            @foreach($specRows as $spec)
                                <div class="row vehicle-spec-row align-items-end">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Attribute Name</label>
                                            <input type="text" name="specification_names[]" class="form-control" value="{{ $spec['name'] ?? '' }}" placeholder="Auction Grade">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Attribute Value</label>
                                            <input type="text" name="specification_values[]" class="form-control" value="{{ $spec['value'] ?? '' }}" placeholder="4.5">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger remove-spec-row">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="addSpecRow" class="btn btn-outline-primary mb-2">Add More</button>
                    </div>
                </div>

                <h5 class="mt-2 mb-1">Display & Content</h5>
                <div class="row">
                    <div class="col-md-3"><div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control">
                            @foreach(['published' => 'Published', 'draft' => 'Draft', 'sold' => 'Sold'] as $value => $label)
                                <option value="{{ $value }}" {!! matchSelected($value, old('status', $data->status)) !!}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $data->sort_order ?? 0) }}"></div></div>
                    <div class="col-md-3"><div class="form-group">
                        <label>Home Page Section</label>
                        <select name="home_section" class="form-control">
                            <option value="">None</option>
                            @foreach($homeSectionOptions as $value => $label)
                                <option value="{{ $value }}" {!! matchSelected($value, old('home_section', $data->home_section)) !!}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div></div>
                    <div class="col-md-3"><div class="form-group mt-2">
                        <fieldset class="checkbox">
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $data->is_featured) ? 'checked' : '' }}>
                                <span class="vs-checkbox"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
                                <span>Show on Home Page</span>
                            </div>
                        </fieldset>
                    </div></div>
                    <div class="col-md-12"><div class="form-group"><label>Short Description</label><textarea name="short_description" rows="3" class="form-control">{{ old('short_description', $data->short_description) }}</textarea></div></div>
                    <div class="col-md-12"><div class="form-group"><label>Full Description</label><textarea name="description" rows="5" class="form-control editor-tinymce">{{ old('description', $data->description) }}</textarea></div></div>
                </div>

                <h5 class="mt-2 mb-1">SEO Meta</h5>
                <div class="row">
                    <div class="col-md-6"><div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $data->meta_title) }}" maxlength="192">
                    </div></div>
                    <div class="col-md-6"><div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $data->meta_keywords) }}" placeholder="Toyota, hybrid, Japan import">
                        <small class="text-muted">Add multiple keywords separated by commas.</small>
                    </div></div>
                    <div class="col-md-12"><div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" rows="3" class="form-control" maxlength="1000">{{ old('meta_description', $data->meta_description) }}</textarea>
                    </div></div>
                </div>

                <h5 class="mt-2 mb-1">Pictures</h5>
                <div class="row">
                    <div class="col-md-12"><div class="form-group">
                        <label>Upload Multiple Pictures {{ $data->exists ? '' : '*' }}</label>
                        <input type="file" name="images[]" id="productImages" class="form-control" accept="image/*" multiple {{ $data->exists ? '' : 'required' }}>
                        <small class="text-muted">You can select more than one image.</small>
                    </div></div>
                    <div class="col-md-12">
                        <div id="newImagePreview" class="row"></div>
                    </div>
                    @if($data->images)
                        <div class="col-md-12">
                            <label>Current Pictures</label>
                            <div class="row" id="existingImages">
                                @foreach($data->images as $image)
                                    <div class="col-md-2 mb-1 existing-image-card">
                                        <label class="d-block border rounded p-1 position-relative">
                                            <button type="button" class="btn btn-danger btn-sm remove-existing-image" style="position:absolute;right:6px;top:6px;border-radius:50%;padding:2px 7px;z-index:2">x</button>
                                            <img src="{{ asset(uploadsDir('front') . $image) }}" alt="Product image" class="img-fluid mb-1">
                                            <input class="existing-image-input" type="checkbox" name="existing_images[]" value="{{ $image }}" checked> Keep
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary mr-1">Save Changes</button>
                <a href="{{ $form['cancel_url'] }}" class="btn btn-danger" style="color:white">Cancel</a>
            </form>
        </div></div>
    </div></div></div>
</section>
@endsection

@section('footer-js')
<style>
    .product-preview-card{position:relative;margin-bottom:12px}
    .product-preview-card img{width:100%;height:120px;object-fit:cover;border:1px solid #ddd;border-radius:6px}
    .product-preview-card button{position:absolute;right:6px;top:6px;border-radius:50%;padding:2px 7px}
</style>
<script>
    const allModelOptions = $('#vehicle_model_id option').clone();

    function filterModels() {
        const brandId = $('#vehicle_brand_id').val();
        const model = $('#vehicle_model_id');
        const selectedValue = model.val();

        model.empty();

        allModelOptions.each(function () {
            const option = $(this);
            const optionBrand = option.data('brand');
            if (!optionBrand || !brandId || String(optionBrand) === String(brandId)) {
                model.append(option.clone());
            }
        });

        if (model.find('option[value="' + selectedValue + '"]').length) {
            model.val(selectedValue);
        } else {
            model.val('');
        }

        model.trigger('change.select2');
    }

    $('#vehicle_brand_id').on('change', filterModels);
    filterModels();

    const imageInput = document.getElementById('productImages');
    const preview = document.getElementById('newImagePreview');
    let selectedFiles = [];

    function refreshImageInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        imageInput.files = dataTransfer.files;
    }

    function renderPreview() {
        preview.innerHTML = '';
        selectedFiles.forEach(function (file, index) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const wrapper = document.createElement('div');
                wrapper.className = 'col-md-2 product-preview-card';
                wrapper.innerHTML = '<button type="button" class="btn btn-danger btn-sm" data-index="' + index + '">x</button><img src="' + event.target.result + '" alt="New product image"><small class="d-block mt-25">' + file.name + '</small>';
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            selectedFiles = Array.from(imageInput.files);
            renderPreview();
        });
    }

    preview.addEventListener('click', function (event) {
        if (event.target.matches('button[data-index]')) {
            selectedFiles.splice(Number(event.target.dataset.index), 1);
            refreshImageInput();
            renderPreview();
        }
    });

    document.querySelectorAll('.remove-existing-image').forEach(function (button) {
        button.addEventListener('click', function () {
            const card = button.closest('.existing-image-card');
            card.querySelector('.existing-image-input').checked = false;
            card.style.display = 'none';
        });
    });

    const specWrapper = document.getElementById('vehicleSpecifications');
    const addSpecButton = document.getElementById('addSpecRow');

    function bindSpecRemoveButtons() {
        document.querySelectorAll('.remove-spec-row').forEach(function (button) {
            button.onclick = function () {
                const rows = specWrapper.querySelectorAll('.vehicle-spec-row');
                if (rows.length === 1) {
                    rows[0].querySelectorAll('input').forEach(input => input.value = '');
                    return;
                }

                button.closest('.vehicle-spec-row').remove();
            };
        });
    }

    addSpecButton.addEventListener('click', function () {
        const template = document.createElement('div');
        template.className = 'row vehicle-spec-row align-items-end';
        template.innerHTML = '<div class="col-md-5"><div class="form-group"><label>Attribute Name</label><input type="text" name="specification_names[]" class="form-control" placeholder="Auction Grade"></div></div><div class="col-md-5"><div class="form-group"><label>Attribute Value</label><input type="text" name="specification_values[]" class="form-control" placeholder="4.5"></div></div><div class="col-md-2"><div class="form-group"><button type="button" class="btn btn-danger remove-spec-row">Remove</button></div></div>';
        specWrapper.appendChild(template);
        bindSpecRemoveButtons();
    });

    bindSpecRemoveButtons();
</script>
@endsection
