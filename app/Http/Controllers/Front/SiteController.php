<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CartItem;
use App\Models\ContactUs;
use App\Models\NewsLetter;
use App\Models\Product;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\WishlistItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['brand', 'vehicleModel'])
            ->published()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $bestSellerProducts = $this->homeSectionProducts('best_seller');
        $upcomingCollectionProducts = $this->homeSectionProducts('upcoming_collection');
        $newArrivalProducts = $this->homeSectionProducts('new_in_demand_arrivals');
        $homeBlogs = Blog::active()->orderByDesc('created_at')->take(12)->get();
        $cartProductIds = CartItem::where('session_id', request()->session()->getId())->pluck('product_id')->all();
        $wishlistProductIds = WishlistItem::where('session_id', request()->session()->getId())->pluck('product_id')->all();
        $homeFilterBrands = VehicleBrand::published()->orderBy('name')->get();
        $homeFilterModels = VehicleModel::with('brand')->published()->orderBy('name')->get();
        $homeFilterYears = Product::published()->whereNotNull('year')->pluck('year')->unique()->sortDesc()->values();

        return view('front.index', compact(
            'featuredProducts',
            'bestSellerProducts',
            'upcomingCollectionProducts',
            'newArrivalProducts',
            'homeBlogs',
            'cartProductIds',
            'wishlistProductIds',
            'homeFilterBrands',
            'homeFilterModels',
            'homeFilterYears'
        ));
    }

    public function aboutUs()
    {
        return view ('front.about-us.about-us');
    }

    public function officialPartners()
    {
        return view('front.official-partners.official-partners');
    }

    private function homeSectionProducts(string $section)
    {
        return Product::with(['brand', 'vehicleModel'])
            ->published()
            ->where('home_section', $section)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(12)
            ->get();
    }
    public function shop(Request $request)
    {
        $allProducts = Product::with(['brand', 'vehicleModel'])
            ->published()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $selectedBrands = array_values(array_filter((array) $request->input('brands', [])));
        $selectedTypes = array_values(array_filter((array) $request->input('types', [])));
        $selectedGrades = array_values(array_filter((array) $request->input('grades', [])));
        $selectedModel = $request->input('model');
        $selectedYear = $request->input('year');
        $selectedMileage = $request->input('max_mileage');
        $selectedTransmission = $request->input('transmission');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort', 'default');
        $search = trim((string) $request->input('search', ''));

        $years = $allProducts->pluck('year')->filter()->unique()->sortDesc()->values();
        $maxAvailablePrice = max(1, (int) $allProducts->max('price'));
        $types = $allProducts
            ->map(fn ($product) => $this->vehicleTypeLabel($product->body_type))
            ->filter()
            ->unique()
            ->sort()
            ->values();
        $grades = $allProducts
            ->map(fn ($product) => $this->productGrade($product))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $filteredProducts = $allProducts
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
            ->when($selectedBrands, fn ($collection) => $collection->whereIn('brand.name', $selectedBrands))
            ->when($selectedModel, fn ($collection) => $collection->where('vehicleModel.name', $selectedModel))
            ->when($selectedYear, fn ($collection) => $collection->where('year', (int) $selectedYear))
            ->when($selectedTypes, fn ($collection) => $collection->filter(fn ($product) => in_array($this->vehicleTypeLabel($product->body_type), $selectedTypes, true)))
            ->when($selectedGrades, fn ($collection) => $collection->filter(fn ($product) => in_array($this->productGrade($product), $selectedGrades, true)))
            ->when($selectedMileage, fn ($collection) => $collection->filter(fn ($product) => $product->mileage !== null && $product->mileage !== '' && (int) $product->mileage <= (int) $selectedMileage))
            ->when($selectedTransmission, fn ($collection) => $collection->filter(fn ($product) => Str::contains(Str::lower((string) $product->transmission), Str::lower($selectedTransmission))))
            ->when($minPrice !== null && $minPrice !== '', fn ($collection) => $collection->filter(fn ($product) => (float) $product->price >= (float) $minPrice))
            ->when($maxPrice !== null && $maxPrice !== '', fn ($collection) => $collection->filter(fn ($product) => (float) $product->price <= (float) $maxPrice));

        $filteredProducts = match ($sort) {
            'price-low' => $filteredProducts->sortBy('price'),
            'price-high' => $filteredProducts->sortByDesc('price'),
            'year-new' => $filteredProducts->sortByDesc('year'),
            default => $filteredProducts,
        };

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $products = new LengthAwarePaginator(
            $filteredProducts->forPage($page, $perPage)->values(),
            $filteredProducts->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $brands = VehicleBrand::published()->orderBy('name')->get();
        $models = VehicleModel::with('brand')->published()->orderBy('name')->get();
        $cartProductIds = CartItem::where('session_id', request()->session()->getId())->pluck('product_id')->all();
        $wishlistProductIds = WishlistItem::where('session_id', request()->session()->getId())->pluck('product_id')->all();

        return view('front.shop.shop', compact(
            'products',
            'brands',
            'models',
            'years',
            'types',
            'grades',
            'maxAvailablePrice',
            'selectedBrands',
            'selectedTypes',
            'selectedGrades',
            'selectedModel',
            'selectedYear',
            'selectedMileage',
            'selectedTransmission',
            'minPrice',
            'maxPrice',
            'sort',
            'search',
            'cartProductIds',
            'wishlistProductIds'
        ));
    }

    public function searchSuggestions(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        if (Str::length($search) < 2) {
            return response()->json([]);
        }

        $products = Product::with(['brand', 'vehicleModel'])
            ->published()
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('stock_no', 'like', '%' . $search . '%')
                    ->orWhere('chassis_no', 'like', '%' . $search . '%')
                    ->orWhere('fuel_type', 'like', '%' . $search . '%')
                    ->orWhere('body_type', 'like', '%' . $search . '%')
                    ->orWhereHas('brand', fn ($brand) => $brand->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('vehicleModel', fn ($model) => $model->where('name', 'like', '%' . $search . '%'));
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->limit(6)
            ->get()
            ->map(fn ($product) => [
                'title' => $product->title,
                'brand' => optional($product->brand)->name,
                'model' => optional($product->vehicleModel)->name,
                'price' => '$' . number_format($product->price, 2),
                'image' => $product->primary_image_url,
                'url' => route('product.show', $product),
            ]);

        return response()->json($products);
    }

    private function vehicleTypeLabel(?string $type): ?string
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

    private function productGrade(Product $product): ?string
    {
        foreach (($product->vehicle_specifications ?: []) as $specification) {
            if (Str::lower(trim((string) ($specification['name'] ?? ''))) === 'grade') {
                $grade = trim((string) ($specification['value'] ?? ''));

                return $grade !== '' ? $grade : null;
            }
        }

        return null;
    }

    public function productDetail(Product $product)
    {
        abort_if($product->status !== 'published', 404);

        $product->load(['brand', 'vehicleModel']);

        $relatedProducts = Product::with(['brand', 'vehicleModel'])
            ->published()
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('vehicle_brand_id', $product->vehicle_brand_id)
                    ->orWhere('vehicle_model_id', $product->vehicle_model_id);
            })
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $cartProductIds = CartItem::where('session_id', request()->session()->getId())->pluck('product_id')->all();
        $wishlistProductIds = WishlistItem::where('session_id', request()->session()->getId())->pluck('product_id')->all();

        return view('front.product.show', compact('product', 'relatedProducts', 'cartProductIds', 'wishlistProductIds'));
    }

    public function services()
    {
        return view ('front.services.services');
    }

    public function blog(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $blogs = Blog::active()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        $featuredBlog = Blog::active()->orderByDesc('created_at')->first();
        $recentBlogs = Blog::active()->orderByDesc('created_at')->take(5)->get();

        return view('front.blog.blog', compact('blogs', 'featuredBlog', 'recentBlogs', 'search'));
    }

    public function blogDetail(Blog $blog)
    {
        abort_if($blog->status !== 'published', 404);

        $recentBlogs = Blog::active()
            ->where('id', '!=', $blog->id)
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        return view('front.blog.show', compact('blog', 'recentBlogs'));
    }

    public function ourLocations()
    {
        return view ('front.our-locations.our-locations');
    }

    public function bank()
    {
        return view('front.bank.bank');
    }

    public function contactUs()
    {
        return view ('front.contact-us.contact-us');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192'],
            'phone' => ['nullable', 'string', 'max:192'],
            'subject' => ['nullable', 'string', 'max:192'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $nameParts = preg_split('/\s+/', trim($validated['name']), 2);
        $message = trim($validated['message'] ?? '');

        if (!empty($validated['subject'])) {
            $message = 'Subject: ' . $validated['subject'] . ($message ? "\n\n" . $message : '');
        }

        ContactUs::create([
            'first_name' => $nameParts[0] ?? $validated['name'],
            'last_name' => $nameParts[1] ?? '-',
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '-',
            'message' => $message,
        ]);

        return back()->with('success', 'Thank you. Your message has been submitted successfully.');
    }

    public function submitNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:192'],
        ]);

        NewsLetter::firstOrCreate([
            'email' => Str::lower($validated['email']),
        ]);

        return back()->with('success', 'Thank you for subscribing to our newsletter.');
    }
}
