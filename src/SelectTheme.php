<?php

declare(strict_types = 1);

namespace Brcas\Select;

use Brcas\Select\Theme\InputThemeDomain;
use Brcas\Select\Theme\ItemThemeDomain;
use Brcas\Select\Theme\ThemeDomain;

final class SelectTheme
{
    private array $blocks = [
        // Input wrapper
        'input_border'  => 'border shadow-sm focus-within:ring-1 border-gray-300 focus-within:border-blue-500 focus-within:ring-blue-500',
        'input_color'   => 'bg-white',
        'input_padding' => 'px-2 py-1',
        'input_extra'   => '',

        // Search input inside wrapper
        'input_search_text' => 'text-sm',

        // Dropdown panel
        'dropdown_border' => 'border border-gray-200',
        'dropdown_color'  => 'bg-white',
        'dropdown_extra'  => '',

        // Item states (Alpine-driven)
        'item_hover'         => 'hover:bg-blue-50 hover:text-blue-700',
        'item_selected'      => 'bg-blue-50 text-blue-700',
        'item_selected_icon' => 'text-blue-600',
        'item_color'         => 'text-gray-700',
        'item_padding'       => 'px-4 py-2',
        'item_text'          => 'text-sm',
        'item_extra'         => '',

        // Placeholder option
        'placeholder' => 'italic text-gray-400',

        // Tag (multi-select chips)
        'tag_color'   => 'bg-blue-100 text-blue-700 hover:text-blue-900',
        'tag_padding' => 'px-2 py-0.5',
        'tag_text'    => 'text-xs font-medium',
        'tag_extra'   => '',

        // Footer (pagination)
        'footer_border'  => 'border-t border-gray-100',
        'footer_color'   => 'bg-white text-gray-400',
        'footer_padding' => 'px-3 py-1.5',
        'footer_text'    => 'text-xs',
        'footer_extra'   => '',

        // Empty / no-results
        'empty_border'  => 'border border-gray-200',
        'empty_color'   => 'bg-white text-gray-500',
        'empty_padding' => 'px-4 py-3',
        'empty_text'    => 'text-sm',
        'empty_extra'   => '',

        // Validation error
        'error_color' => 'text-red-600 dark:text-red-400',
        'error_text'  => 'text-sm',
        'error_extra' => '',
    ];

    private ?string $current = null;

    // -------------------------------------------------------------------------
    // Domain builder entry points
    // -------------------------------------------------------------------------

    public function input(): InputThemeDomain
    {
        return new InputThemeDomain($this->writer(), 'input');
    }

    public function dropdown(): ThemeDomain
    {
        return new ThemeDomain($this->writer(), 'dropdown');
    }

    public function item(): ItemThemeDomain
    {
        return new ItemThemeDomain($this->writer(), 'item');
    }

    public function tag(): ThemeDomain
    {
        return new ThemeDomain($this->writer(), 'tag');
    }

    public function footer(): ThemeDomain
    {
        return new ThemeDomain($this->writer(), 'footer');
    }

    public function empty(): ThemeDomain
    {
        return new ThemeDomain($this->writer(), 'empty');
    }

    public function error(): ThemeDomain
    {
        return new ThemeDomain($this->writer(), 'error');
    }

    public function placeholder(string $value): static
    {
        $this->blocks['placeholder'] = $value;

        return $this;
    }

    // -------------------------------------------------------------------------
    // Targeted block manipulation (power-user API)
    // -------------------------------------------------------------------------

    public function block(string $name): static
    {
        $this->current = $name;

        return $this;
    }

    public function replace(array | string $from, ?string $to = null): static
    {
        $map = is_array($from) ? $from : [$from => $to];

        foreach ($map as $old => $new) {
            $this->blocks[$this->current] = str_replace($old, (string) $new, $this->blocks[$this->current] ?? '');
        }

        $this->current = null;

        return $this;
    }

    public function append(string $content): static
    {
        $this->blocks[$this->current] = mb_trim(($this->blocks[$this->current] ?? '') . ' ' . $content);
        $this->current                = null;

        return $this;
    }

    public function prepend(string $content): static
    {
        $this->blocks[$this->current] = mb_trim($content . ' ' . ($this->blocks[$this->current] ?? ''));
        $this->current                = null;

        return $this;
    }

    public function remove(array | string $classes): static
    {
        foreach ((array) $classes as $class) {
            $this->blocks[$this->current] = mb_trim(str_replace($class, '', $this->blocks[$this->current] ?? ''));
        }

        $this->current = null;

        return $this;
    }

    public function get(string $block): string
    {
        return $this->blocks[$block] ?? '';
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return $this->blocks;
    }

    // -------------------------------------------------------------------------

    private function writer(): \Closure
    {
        return function (string $key, string $value): void {
            $this->blocks[$key] = $value;
        };
    }
}
