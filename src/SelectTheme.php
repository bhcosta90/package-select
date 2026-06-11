<?php

declare(strict_types = 1);

namespace Brcas\Select;

final class SelectTheme
{
    private array $blocks = [
        'input_wrapper_base' => 'border shadow-sm focus-within:ring-1',
        'input'              => 'border-gray-300 focus-within:border-blue-500 focus-within:ring-blue-500',
        'input_search_size'  => 'text-sm',
        'dropdown'           => 'border-gray-200',
        'item_hover'         => 'hover:bg-blue-50 hover:text-blue-700',
        'item_selected'      => 'bg-blue-50 text-blue-700',
        'item_selected_icon' => 'text-blue-600',
        'item_text_size'     => 'text-sm',
        'placeholder'        => 'italic text-gray-400',
        'tag'                => 'bg-blue-100 text-blue-700 hover:text-blue-900',
        'footer'             => 'border-gray-100 bg-white text-gray-400',
        'empty'              => 'border-gray-200 bg-white text-gray-500',
        'error'              => 'text-sm text-red-600 dark:text-red-400',
    ];

    private ?string $current = null;

    public function customize(): static
    {
        return $this;
    }

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

    public function set(string $name, string $value): static
    {
        $this->blocks[$name] = $value;

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
}
