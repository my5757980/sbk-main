@php
    $adminUser = auth('admin')->user();
    $canAdmin = fn ($permission) => !$adminUser || $adminUser->hasAdminPermission($permission);
    $showInventoryMenu = $canAdmin('product.create') || $canAdmin('product.index');
    $showUsersMenu = $canAdmin('users.create') || $canAdmin('users.index');
    $showOrdersMenu = $canAdmin('orders.create') || $canAdmin('orders.index');
    $showVehicleBrandMenu = $canAdmin('vehicle-brand.index');
    $showVehicleModelMenu = $canAdmin('vehicle-model.index');
    $showBlogMenu = $canAdmin('blog.create') || $canAdmin('blog.index');
    $showContactMenu = $canAdmin('contact-us.index');
    $showFaqMenu = $canAdmin('faq.create') || $canAdmin('faq.index');
    $showPagesMenu = $canAdmin('pages.index');
    $showModulesMenu = $showInventoryMenu || $showVehicleBrandMenu || $showVehicleModelMenu || $showUsersMenu || $showOrdersMenu || $showBlogMenu || $showContactMenu || $showFaqMenu || $showPagesMenu;
    $showSettingsMenu = $canAdmin('admin-roles.index') || $canAdmin('administrators.index') || $canAdmin('site-settings.index');
@endphp
<style>
    .sbk-crm-global-btn {
        background: #d71920 !important;
        border-color: #d71920 !important;
        color: #fff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(215, 25, 32, .24);
    }

    .sbk-crm-global-btn:hover,
    .sbk-crm-global-btn:focus {
        background: #111216 !important;
        border-color: #111216 !important;
        color: #fff !important;
    }
</style>
<!-- BEGIN: Header-->
<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                                class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i
                                    class="ficon feather icon-menu"></i></a></li>
                    </ul>

                    <ul class="nav navbar-nav">
                        <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i
                                    class="ficon feather icon-maximize"></i></a></li>
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                    <li class="nav-item d-none d-sm-flex align-items-center mr-1">
                        <a class="btn btn-sm sbk-crm-global-btn" href="https://crmglobal.sbkautotrading.com/login?callbackUrl=https%3A%2F%2Fcrmglobal.sbkautotrading.com%2F">
                            CRM Global
                        </a>
                    </li>
                    <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                            href="#" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none"><span
                                    class="user-name text-bold-600">{!! $adminUser->first_name . ' ' . $adminUser->last_name !!}</span></div><span>
                                @if ($adminUser->image != '' && file_exists(uploadsDir('admin') . $adminUser->image))
                                    <img class="round" src="{!! asset(uploadsDir('admin') . $adminUser->image) !!}" alt="avatar" height="40"
                                        width="40">
                            </span>
                        @else
                            <img class="round" src="{!! asset('assets/admin/app-assets/images/portrait/small/avatar-s-11.jpg') !!}" alt="avatar" height="40"
                                width="40"></span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item"
                                href="{!! route('admin.update-profile') !!}"><i class="feather icon-user"></i> Edit Profile</a>
                            <div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;"
                                onclick="logout()"><i class="feather icon-power"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- END: Header-->


<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <a class="navbar-brand brand-logo" href="#"
            style="background: url('{{ $siteSettings->logo_path }}') no-repeat; background-position: center; height: 70px; width: auto;background-size: contain;"></a>
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="#">
                    <div class="brand-logo"></div>
                    {{--                    <h2 class="brand-text mb-0">{{ $siteSettings->site_title }}</h2> --}}
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                        class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i
                        class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
                        data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            @if($canAdmin('dashboard.view'))
            <li class=" nav-item {{ request()->segment(2) == 'dashboard' ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard.index') }}">
                    <i class="feather icon-home"></i>
                    <span class="menu-title" data-i18n="Dashboard">
                        Dashboard
                    </span>
                </a>
            </li>
            @endif

            @if($showModulesMenu)
            <li class=" navigation-header"><span>Modules</span>
            </li>
            @endif
            @if($showInventoryMenu)
            <li class="nav-item {{ request()->segment(2) == 'product' ? 'active' : '' }}">
                <a href="#"><i class="feather icon-truck"></i><span class="menu-title" data-i18n="Inventory">Inventory</span></a>
                <ul class="menu-content">
                    @if($canAdmin('product.create'))
                    <li class="{{ request()->segment(2) == 'product' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.product.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Add Product">Add Product</span></a>
                    </li>
                    @endif
                    @if($canAdmin('product.index'))
                    <li class="{{ request()->segment(2) == 'product' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.product.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Products">Products</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if($showVehicleBrandMenu)
            <li class="nav-item {{ request()->segment(2) == 'vehicle-brand' ? 'active' : '' }}">
                <a href="{{ route('admin.vehicle-brand.index') }}">
                    <i class="feather icon-tag"></i>
                    <span class="menu-title" data-i18n="Brands">Brands</span>
                </a>
            </li>
            @endif
            @if($showVehicleModelMenu)
            <li class="nav-item {{ request()->segment(2) == 'vehicle-model' ? 'active' : '' }}">
                <a href="{{ route('admin.vehicle-model.index') }}">
                    <i class="feather icon-grid"></i>
                    <span class="menu-title" data-i18n="Models">Models</span>
                </a>
            </li>
            @endif
            @if($showUsersMenu)
            <li class="nav-item {{ request()->segment(2) == 'users' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-user"></i><span class="menu-title" data-i18n="User">Users</span></a>
                <ul class="menu-content">
                    @if($canAdmin('users.create'))
                    <li
                        class="{{ request()->segment(2) == 'users' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.users.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Add User">Add User</span></a>
                    </li>
                    @endif
                    @if($canAdmin('users.index'))
                    <li
                        class="{{ request()->segment(2) == 'users' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if($showOrdersMenu)
            <li class="nav-item {{ request()->segment(2) == 'orders' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-shopping-bag"></i><span class="menu-title" data-i18n="Orders">Orders</span></a>
                <ul class="menu-content">
                    @if($canAdmin('orders.create'))
                    <li
                        class="{{ request()->segment(2) == 'orders' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Add Order">Add Order</span></a>
                    </li>
                    @endif
                    @if($canAdmin('orders.index'))
                    <li
                        class="{{ request()->segment(2) == 'orders' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="Orders">Orders</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            {{--            <li class="nav-item {{ request()->segment(2) == 'blog' ? 'active' : '' }}"> <a href="#"><i class="feather icon-cast"></i><span class="menu-title" data-i18n="User">Blogs</span></a> --}}
            {{--                <ul class="menu-content"> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'blog' && request()->segment(3) == 'create') ? 'active' : '' }}"><a href="{{ route('admin.blog.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
            {{--                    </li> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'blog' && request()->segment(3) != 'create') ? 'active' : '' }}"><a href="{{ route('admin.blog.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">List</span></a> --}}
            {{--                    </li> --}}
            {{--                </ul> --}}
            {{--            </li> --}}
            @if($showBlogMenu)
            <li class="nav-item {{ request()->segment(2) == 'blog' ? 'active' : '' }}"> <a href="#"><i class="feather icon-file-text"></i><span class="menu-title" data-i18n="Blogs">Blogs</span></a>
                <ul class="menu-content">
                    @if($canAdmin('blog.create'))
                    <li class="{{ request()->segment(2) == 'blog' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.blog.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Add Blog">Add New</span></a>
                    </li>
                    @endif
                    @if($canAdmin('blog.index'))
                    <li class="{{ request()->segment(2) == 'blog' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.blog.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="Blogs">List</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if($showContactMenu)
            <li class="nav-item {{ request()->segment(2) == 'contact-us' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-cast"></i><span class="menu-title" data-i18n="User">Contact</span></a>
                <ul class="menu-content">
                    @if($canAdmin('contact-us.index'))
                    <li
                        class="{{ request()->segment(2) == 'contact-us' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.contact-us.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            {{--            <li class="nav-item {{ request()->segment(2) == 'newsletters' ? 'active' : '' }}"> <a href="#"><i class="feather icon-cast"></i><span class="menu-title" data-i18n="User">NewsLetter</span></a> --}}
            {{--                <ul class="menu-content"> --}}
            {{--                    --}}{{--                    <li class="{{ (request()->segment(2) == 'contact-us' && request()->segment(3) == 'create') ? 'active' : '' }}"><a href="{{ route('admin.contact-us.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
            {{--                    --}}{{--                    </li> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'newsletters' && request()->segment(3) != 'create') ? 'active' : '' }}"><a href="{{ route('admin.newsletters.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">List</span></a> --}}
            {{--                    </li> --}}
            {{--                </ul> --}}
            {{--            </li> --}}
            {{--            <li class="nav-item {{ request()->segment(2) == 'testimonial' ? 'active' : '' }}"> <a href="#"><i class="feather icon-user"></i><span class="menu-title" data-i18n="User">Testimonial</span></a> --}}
            {{--                <ul class="menu-content"> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'testimonial' && request()->segment(3) == 'create') ? 'active' : '' }}"><a href="{{ route('admin.testimonial.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
            {{--                    </li> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'testimonial' && request()->segment(3) != 'create') ? 'active' : '' }}"><a href="{{ route('admin.testimonial.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">List</span></a> --}}
            {{--                    </li> --}}
            {{--                </ul> --}}
            {{--            </li> --}}
            <!-- <li class="nav-item {{ request()->segment(2) == 'newsletters' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-mail"></i><span class="menu-title" data-i18n="User">Newsletter</span></a>
                <ul class="menu-content">
                    <li class="{{ request()->segment(2) == 'newsletters' ? 'active' : '' }}">
                        <a href="{{ route('admin.newsletters.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                </ul>
            </li>             -->
            <!-- <li class="nav-item {{ request()->segment(2) == 'resource' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-briefcase"></i><span class="menu-title" data-i18n="User">Resource</span></a>
                <ul class="menu-content">
                    <li
                        class="{{ request()->segment(2) == 'resource' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.resource.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="List">Add New</span></a>
                    </li>
                    <li
                        class="{{ request()->segment(2) == 'resource' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.resource.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->segment(2) == 'activity' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-box"></i><span class="menu-title" data-i18n="User">Action</span></a>
                <ul class="menu-content">
                    <li
                        class="{{ request()->segment(2) == 'activity' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.activity.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="List">Add New</span></a>
                    </li>
                    <li
                        class="{{ request()->segment(2) == 'activity' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.activity.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->segment(2) == 'feeling' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-circle"></i><span class="menu-title" data-i18n="User">Thoughts</span></a>
                <ul class="menu-content">
                    <li
                        class="{{ request()->segment(2) == 'feeling' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.feeling.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="List">Add New</span></a>
                    </li>
                    <li
                        class="{{ request()->segment(2) == 'feeling' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.feeling.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->segment(2) == 'category' ? 'active' : '' }}"> <a href="#"><i
                        class="feather icon-codepen"></i><span class="menu-title"
                        data-i18n="User">Category</span></a>
                <ul class="menu-content">
                    <li
                        class="{{ request()->segment(2) == 'category' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.category.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="List">Add New</span></a>
                    </li>
                    <li
                        class="{{ request()->segment(2) == 'category' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.category.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ request()->segment(2) == 'amazing-feature' ? 'active' : '' }}"> <a
                    href="#"><i class="feather icon-gift"></i><span class="menu-title"
                        data-i18n="User">Amazing Feature</span></a>
                <ul class="menu-content">
                    <li
                        class="{{ request()->segment(2) == 'amazing-feature' && request()->segment(3) == 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.amazing-feature.create') }}"><i
                                class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add
                                New</span></a>
                    </li>
                    <li
                        class="{{ request()->segment(2) == 'amazing-feature' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.amazing-feature.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                </ul>
            </li> -->

            @if($showFaqMenu)
            <li class="nav-item {{ request()->segment(2) == 'faq' ? 'active' : '' }}"><a href="#"><i
                        class="feather icon-activity"></i><span class="menu-title"
                        data-i18n="Media player">FAQ's</span>
                </a>
                <ul class="menu-content">
                    @if($canAdmin('faq.create'))
                    <li><a href="{{ route('admin.faq.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="List">Add New</span></a>
                    </li>
                    @endif
                    @if($canAdmin('faq.index'))
                    <li
                        class="{{ request()->segment(2) == 'faq' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.faq.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            {{--            <li class="nav-item {{ request()->segment(2) == 'relation-question' ? 'active' : '' }}"><a href="#"><i class="feather icon-activity"></i><span class="menu-title" data-i18n="Media player">Relation Question</span> --}}
            {{--                </a> --}}
            {{--                <ul class="menu-content"> --}}
            {{--                    <li><a href="{{ route('admin.relation-question.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
            {{--                    </li> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'relation-question' && request()->segment(3) != 'create') ? 'active' : '' }}"><a href="{{ route('admin.relation-question.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">List</span></a> --}}
            {{--                    </li> --}}
            {{--                </ul> --}}
            {{--            </li> --}}
            {{--            <li class="nav-item {{ request()->segment(2) == 'situation-question' ? 'active' : '' }}"><a href="#"><i class="feather icon-activity"></i><span class="menu-title" data-i18n="Media player">Situation Question</span> --}}
            {{--                </a> --}}
            {{--                <ul class="menu-content"> --}}
            {{--                    <li><a href="{{ route('admin.situation-question.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
            {{--                    </li> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'situation-question' && request()->segment(3) != 'create') ? 'active' : '' }}"><a href="{{ route('admin.situation-question.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">List</span></a> --}}
            {{--                    </li> --}}
            {{--                </ul> --}}
            {{--            </li> --}}
            <!-- <li class="nav-item {{ request()->segment(2) == 'question' ? 'active' : '' }}"><a href="#"><i
                        class="feather icon-compass"></i><span class="menu-title" data-i18n="Media player">Survey
                        Questions</span>
                </a>
                <ul class="menu-content"> -->
                    {{--                    <li class="{{ (request()->segment(2) == 'relation-question' && request()->segment(3) != 'create') ? 'active' : '' }}"><a href="{{ route('admin.relation-question.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">Relation</span></a> --}}
                    {{--                    </li> --}}
                    <!-- <li
                        class="{{ request()->segment(2) == 'situation-question' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.situation-question.index') }}"><i
                                class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="List">Situation</span></a>
                    </li>
                    <li
                        class="{{ request()->segment(2) == 'habit-question' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.habit-question.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">Habit</span></a>
                    </li> -->
                <!-- </ul>
            </li> -->

            {{--            <li class="nav-item {{ request()->segment(2) == 'question' ? 'active' : '' }}"><a href="#"><i class="feather icon-activity"></i><span class="menu-title" data-i18n="Media player">Question</span> --}}
            {{--                </a> --}}
            {{--                <ul class="menu-content"> --}}
            {{--                    <li><a href="{{ route('admin.question.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
            {{--                    </li> --}}
            {{--                    <li class="{{ (request()->segment(2) == 'faq' && request()->segment(3) != 'create') ? 'active' : '' }}"><a href="{{ route('admin.question.index') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="View">List</span></a> --}}
            {{--                    </li> --}}
            {{--                </ul> --}}
            {{--            </li> --}}
            <!-- <li class="nav-item {{ request()->segment(2) == 'mood' ? 'active' : '' }}"><a href="#"><i
                        class="feather icon-activity"></i><span class="menu-title"
                        data-i18n="Media player">Mood</span>
                </a>
                <ul class="menu-content"> -->
                    {{--                    <li><a href="{{ route('admin.mood.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
                    {{--                    </li> --}}
                    <!-- <li
                        class="{{ request()->segment(2) == 'mood' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.mood.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                </ul>
            </li> -->

            @if($showPagesMenu)
            <li class="nav-item {{ request()->segment(2) == 'pages' ? 'active' : '' }}"><a href="#"><i
                        class="feather icon-globe"></i><span class="menu-title" data-i18n="l18n">Pages</span>
                </a>
                <ul class="menu-content">
                    {{--                    <li class="{{ (request()->segment(2) == 'pages' && request()->segment(3) == 'create') ? 'active' : '' }}"><a href="{{ route('admin.pages.create') }}"><i class="feather icon-circle"></i><span class="menu-item" data-i18n="List">Add New</span></a> --}}
                    {{--                    </li> --}}
                    @if($canAdmin('pages.index'))
                    <li
                        class="{{ request()->segment(2) == 'pages' && request()->segment(3) != 'create' ? 'active' : '' }}">
                        <a href="{{ route('admin.pages.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="View">List</span></a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
          


            @if($showSettingsMenu)
                       <li class="navigation-header"><span>Settings</span>
            </li>
            @endif
            @if($canAdmin('admin-roles.index'))
            <li class="nav-item {{ request()->segment(2) == 'admin-roles' ? 'active' : '' }}">
                <a href="{{ route('admin.admin-roles.index') }}">
                    <i class="feather icon-shield"></i>
                    <span class="menu-title" data-i18n="Roles">Roles & Permissions</span>
                </a>
            </li>
            @endif
            @if($canAdmin('administrators.index'))
            <li class="nav-item {{ request()->segment(2) == 'administrators' ? 'active' : '' }}">
                <a href="{{ route('admin.administrators.index') }}">
                    <i class="feather icon-user-check"></i>
                    <span class="menu-title" data-i18n="Administrators">Administrators</span>
                </a>
            </li>
            @endif
            @if($canAdmin('site-settings.index'))
            <li class="nav-item {{ request()->segment(2) == 'site-settings' ? 'active' : '' }}">
                <a href="{{ route('admin.site-settings.index') }}">
                    <i class="feather icon-settings"></i>
                    <span class="menu-title" data-i18n="Email">Site Settings</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>
