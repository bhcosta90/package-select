<?php

declare(strict_types = 1);

return [
    /*
    |--------------------------------------------------------------------------
    | Search operator
    |--------------------------------------------------------------------------
    | Operator used for search queries across all SelectController endpoints.
    | Use 'like' for MySQL/SQLite or 'ilike' for PostgreSQL (case-insensitive).
    */

    'search_operator' => 'like',

    /*
    |--------------------------------------------------------------------------
    | Default theme for the x-select component
    |--------------------------------------------------------------------------
    | Change values here to update the look of all selects at once.
    | Each value accepts one or more Tailwind CSS classes.
    */

    'input'              => 'border-gray-300 focus-within:border-blue-500 focus-within:ring-blue-500',

    'dropdown'           => 'border-gray-200',

    'item_hover'         => 'hover:bg-blue-50 hover:text-blue-700',
    'item_selected'      => 'bg-blue-50 text-blue-700',
    'item_selected_icon' => 'text-blue-600',

    'placeholder'        => 'italic text-gray-400',

    'tag'                => 'bg-blue-100 text-blue-700 hover:text-blue-900',

    'footer'             => 'border-gray-100 bg-white text-gray-400',

    'empty'              => 'border-gray-200 bg-white text-gray-500',

    'error'              => 'text-sm text-red-600 dark:text-red-400',

];
