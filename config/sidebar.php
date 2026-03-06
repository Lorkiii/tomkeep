<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sidebar menu items
    |--------------------------------------------------------------------------
    | Edit this array to add, remove, or reorder sidebar links. The sidebar
    | component uses these when no custom `items` are passed (e.g. from Livewire).
    |
    | Each item:
    |   - key    : Used to highlight active item (pass active="key" in layout).
    |   - label  : Text shown in the sidebar.
    |   - route  : Laravel route name (preferred), or use 'url' for external links.
    |   - url    : Optional. Use instead of 'route' for external or custom URLs.
    |   - children: Optional. Array of sub-items (same structure: key, label, route/url).
    |
    | Example - add a new link:
    |   ['key' => 'reports', 'label' => 'Reports', 'route' => 'reports.index'],
    |
    | Example - item with children:
    |   ['key' => 'settings', 'label' => 'Settings', 'route' => 'settings', 'children' => [
    |       ['key' => 'profile', 'label' => 'Profile', 'route' => 'profile'],
    |   ]],
    */

    'items' => [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'route' => 'home',
        ],
        [
            'key' => 'account',
            'label' => 'Account Settings',
            'route' => 'account.settings',
        ],
        [
            'key' => 'dtr',
            'label' => 'My Monthly DTR',
            'route' => 'monthly.dtr',
        ],
        [
            'key' => 'terms',
            'label' => 'Terms and Conditions',
            'route' => 'terms.dashboard',
        ],
    ],

];
