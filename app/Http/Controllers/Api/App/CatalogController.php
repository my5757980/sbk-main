<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Product;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Services\App\AppPresenter;
use App\Services\App\ProductSearch;
use Illuminate\Http\Request;

/**
 * What the SBK app shows a visitor: the home rows, the shop, a vehicle, the
 * blog. Read from this website's database with the website's own rules.
 */
class CatalogController extends Controller
{
    /** The home page's rows (SiteController@index). */
    public function home(ProductSearch $search)
    {
        $section = fn (string $name) => Product::with(['brand', 'vehicleModel'])
            ->published()
            ->where('home_section', $name)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(12)
            ->get();

        // With a row empty, the website falls back to its featured vehicles.
        $featured = Product::with(['brand', 'vehicleModel'])
            ->published()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $bestSeller = $section('best_seller');
        $upcoming = $section('upcoming_collection');
        $arrivals = $section('new_in_demand_arrivals');

        $brands = $search->all()
            ->groupBy(fn ($p) => (string) optional($p->brand)->name)
            ->map(fn ($group, $name) => ['name' => $name, 'count' => $group->count()])
            ->filter(fn ($b) => $b['name'] !== '')
            ->sortBy([['count', 'desc'], ['name', 'asc']])
            ->values();

        return response()->json([
            'bestSeller' => ($bestSeller->count() ? $bestSeller : $featured)->map([AppPresenter::class, 'card'])->values(),
            'upcoming' => ($upcoming->count() ? $upcoming : $featured->take(4))->map([AppPresenter::class, 'card'])->values(),
            'newArrivals' => ($arrivals->count() ? $arrivals : $featured->slice(4, 4))->map([AppPresenter::class, 'card'])->values(),
            'blogs' => Blog::active()->orderByDesc('created_at')->take(12)->get()->map([AppPresenter::class, 'blogCard'])->values(),
            'brands' => $brands,
        ]);
    }

    /** Every choice the shop's filters offer (SiteController@shop). */
    public function filters(ProductSearch $search)
    {
        $all = $search->all();

        return response()->json([
            'brands' => VehicleBrand::published()->orderBy('name')->pluck('name')->values(),
            'models' => VehicleModel::with('brand')->published()->orderBy('name')->get()
                ->map(fn ($m) => ['brand' => (string) optional($m->brand)->name, 'name' => $m->name])->values(),
            'types' => $all->map(fn ($p) => ProductSearch::typeLabel($p->body_type))->filter()->unique()->sort()->values(),
            'grades' => $all->map(fn ($p) => ProductSearch::grade($p))->filter()->unique()->sort()->values(),
            'years' => $all->pluck('year')->filter()->unique()->sortDesc()->values()->map(fn ($y) => (int) $y),
            'maxPrice' => max(1, (int) $all->max('price')),
            'total' => $all->count(),
        ]);
    }

    /**
     * The shop: the website's filters and sort, a page at a time. With
     * ?slugs=a,b,c it answers those vehicles in that order instead (the
     * phone's cart and wishlist); vehicles no longer listed drop out.
     */
    public function products(Request $request, ProductSearch $search)
    {
        if ($request->filled('slugs')) {
            $slugs = array_slice(array_filter(array_map('trim', explode(',', (string) $request->query('slugs')))), 0, 100);
            $found = Product::with(['brand', 'vehicleModel'])->published()->whereIn('slug', $slugs)->get()->keyBy('slug');
            $items = collect($slugs)->map(fn ($s) => $found->get($s))->filter()->map([AppPresenter::class, 'card'])->values();

            return response()->json(['items' => $items, 'total' => $items->count(), 'page' => 1, 'perPage' => $items->count(), 'lastPage' => 1]);
        }

        $found = $search->filter($search->all(), $request->only([
            'search', 'brands', 'model', 'year', 'types', 'grades', 'max_mileage', 'transmission', 'min_price', 'max_price', 'sort',
        ]))->values();

        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $page = max(1, (int) $request->query('page', 1));
        $total = $found->count();

        return response()->json([
            'items' => $found->forPage($page, $perPage)->map([AppPresenter::class, 'card'])->values(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    /** A vehicle's page, with its related vehicles (SiteController@productDetail). */
    public function product(string $slug)
    {
        $product = Product::with(['brand', 'vehicleModel'])->where('slug', $slug)->first();
        abort_if(!$product || $product->status !== 'published', 404);

        $related = Product::with(['brand', 'vehicleModel'])
            ->published()
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('vehicle_brand_id', $product->vehicle_brand_id)
                    ->orWhere('vehicle_model_id', $product->vehicle_model_id);
            })
            ->orderByDesc('id')
            ->take(6)
            ->get();

        return response()->json(AppPresenter::detail($product) + [
            'related' => $related->map([AppPresenter::class, 'card'])->values(),
        ]);
    }

    /** The blog, newest first, with its search (SiteController@blog). */
    public function blogs(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $perPage = min(50, max(1, (int) $request->query('per_page', 20)));

        $blogs = Blog::active()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'items' => collect($blogs->items())->map([AppPresenter::class, 'blogCard'])->values(),
            'total' => $blogs->total(),
            'page' => $blogs->currentPage(),
            'perPage' => $blogs->perPage(),
            'lastPage' => $blogs->lastPage(),
        ]);
    }

    /** One article, and the four newest others (SiteController@blogDetail). */
    public function blog(string $slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        abort_if(!$blog || $blog->status !== 'published', 404);

        $recent = Blog::active()->where('id', '!=', $blog->id)->orderByDesc('created_at')->take(4)->get();

        return response()->json(AppPresenter::blog($blog) + [
            'recent' => $recent->map([AppPresenter::class, 'blogCard'])->values(),
        ]);
    }
}
