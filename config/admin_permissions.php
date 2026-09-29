<?php

return [
    'dashboard' => [
        'label' => 'Dashboard',
        'actions' => ['view' => 'View Dashboard'],
    ],
    'product' => [
        'label' => 'Products',
        'actions' => ['index' => 'List Products', 'create' => 'Add Product', 'edit' => 'Edit Product', 'show' => 'View Product', 'destroy' => 'Delete Product'],
    ],
    'vehicle-brand' => [
        'label' => 'Vehicle Brands',
        'actions' => ['index' => 'List Brands', 'create' => 'Add Brand', 'edit' => 'Edit Brand', 'destroy' => 'Delete Brand'],
    ],
    'vehicle-model' => [
        'label' => 'Vehicle Models',
        'actions' => ['index' => 'List Models', 'create' => 'Add Model', 'edit' => 'Edit Model', 'destroy' => 'Delete Model'],
    ],
    'users' => [
        'label' => 'Customers',
        // view_all: without it an admin sees only their own customers (see User::scopeVisibleTo).
        'actions' => ['index' => 'List Customers', 'create' => 'Add Customer', 'edit' => 'Edit Customer', 'show' => 'View Customer', 'destroy' => 'Delete Customer', 'view_all' => 'See all customers'],
    ],
    'orders' => [
        'label' => 'Orders',
        'actions' => ['index' => 'List Orders', 'create' => 'Add Order', 'edit' => 'Edit Order', 'show' => 'View Order', 'destroy' => 'Delete Order'],
    ],
    'blog' => [
        'label' => 'Blogs',
        'actions' => ['index' => 'List Blogs', 'create' => 'Add Blog', 'edit' => 'Edit Blog', 'show' => 'View Blog', 'destroy' => 'Delete Blog'],
    ],
    'contact-us' => [
        'label' => 'Contact',
        'actions' => ['index' => 'List Contact Messages', 'show' => 'View Contact Message', 'destroy' => 'Delete Contact Message'],
    ],
    'newsletters' => [
        'label' => 'Newsletter',
        'actions' => ['index' => 'List Subscribers', 'show' => 'View Subscriber', 'destroy' => 'Delete Subscriber'],
    ],
    'faq' => [
        'label' => 'FAQs',
        'actions' => ['index' => 'List FAQs', 'create' => 'Add FAQ', 'edit' => 'Edit FAQ', 'show' => 'View FAQ', 'destroy' => 'Delete FAQ'],
    ],
    'pages' => [
        'label' => 'Pages',
        'actions' => ['index' => 'List Pages', 'create' => 'Add Page', 'edit' => 'Edit Page', 'show' => 'View Page', 'destroy' => 'Delete Page'],
    ],
    'site-settings' => [
        'label' => 'Site Settings',
        'actions' => ['index' => 'Manage Site Settings', 'edit' => 'Edit Site Settings', 'update' => 'Update Site Settings'],
    ],
    'testimonial' => [
        'label' => 'Testimonials',
        'actions' => ['index' => 'List Testimonials', 'create' => 'Add Testimonial', 'edit' => 'Edit Testimonial', 'show' => 'View Testimonial', 'destroy' => 'Delete Testimonial'],
    ],
    'administrators' => [
        'label' => 'Administrators',
        'actions' => ['index' => 'List Administrators', 'create' => 'Add Administrator', 'edit' => 'Edit Administrator', 'show' => 'View Administrator', 'destroy' => 'Delete Administrator'],
    ],
    'admin-roles' => [
        'label' => 'Roles & Permissions',
        'actions' => ['index' => 'List Roles', 'create' => 'Add Role', 'edit' => 'Edit Role', 'show' => 'View Role', 'destroy' => 'Delete Role'],
    ],
];
