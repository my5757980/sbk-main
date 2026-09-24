<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\CheckoutOrder;
use App\Models\ContactUs;
use App\Models\NewsLetter;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Admin Dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $usersCount = User::count();
        $productCount = Product::count();
        $publishedProductCount = Product::published()->count();
        $blogCount = Blog::count();
        $publishedBlogCount = Blog::active()->count();
        $pageCount = Page::count();
        $ordersCount = CheckoutOrder::count();
        $pendingOrdersCount = CheckoutOrder::where('status', 'pending')->count();
        $paidOrdersCount = CheckoutOrder::whereIn('status', ['paid', 'completed'])->count();
        $totalRevenue = CheckoutOrder::sum('paid_amount');
        $remainingBalance = CheckoutOrder::sum('remaining_amount');
        $contactCount = ContactUs::count();
        $newsletterCount = NewsLetter::count();
        $users = User::orderBy('id', 'DESC')->take(5)->get();
        $recentOrders = CheckoutOrder::with('user')->orderBy('id', 'DESC')->take(5)->get();
        $orderChart = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            $orders = CheckoutOrder::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            return [
                'label' => $date->format('M'),
                'orders' => (clone $orders)->count(),
                'revenue' => (float) (clone $orders)->sum('paid_amount'),
            ];
        });

        return view('admin.dashboard.index', compact(
            'usersCount',
            'productCount',
            'publishedProductCount',
            'blogCount',
            'publishedBlogCount',
            'pageCount',
            'ordersCount',
            'pendingOrdersCount',
            'paidOrdersCount',
            'totalRevenue',
            'remainingBalance',
            'contactCount',
            'newsletterCount',
            'users',
            'recentOrders',
            'orderChart'
        ));
    }
}
