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
    | Each value accepts Tailwind CSS classes.
    */

    'input_border'       => 'border-gray-300',
    'input_focus_border' => 'focus-within:border-blue-500',
    'input_focus_ring'   => 'focus-within:ring-blue-500',

    'dropdown_border' => 'border-gray-200',

    'item_hover_bg'      => 'hover:bg-blue-50',
    'item_hover_text'    => 'hover:text-blue-700',
    'item_selected_bg'   => 'bg-blue-50',
    'item_selected_text' => 'text-blue-700',
    'item_selected_icon' => 'text-blue-600',

    'placeholder_text' => 'italic text-gray-400',

    'tag_bg'         => 'bg-blue-100',
    'tag_text'       => 'text-blue-700',
    'tag_hover_text' => 'hover:text-blue-900',

    'footer_border' => 'border-gray-100',
    'footer_bg'     => 'bg-white',
    'footer_text'   => 'text-gray-400',

    'empty_border' => 'border-gray-200',
    'empty_bg'     => 'bg-white',
    'empty_text'   => 'text-gray-500',

];
