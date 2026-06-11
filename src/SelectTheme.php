<?php

declare(strict_types = 1);

namespace Brcas\Select;

final class SelectTheme
{
    private array $blocks = [
        'input_wrapper_base' => 'border shadow-sm focus-within:ring-1',
        'input_border'       => 'border-gray-300',
        'input_focus_border' => 'focus-within:border-blue-500',
        'input_focus_ring'   => 'focus-within:ring-blue-500',
        'input_search_size'  => 'text-sm',
        'dropdown_border'    => 'border-gray-200',
        'item_hover_bg'      => 'hover:bg-blue-50',
        'item_hover_text'    => 'hover:text-blue-700',
        'item_selected_bg'   => 'bg-blue-50',
        'item_selected_text' => 'text-blue-700',
        'item_selected_icon' => 'text-blue-600',
        'item_text_size'     => 'text-sm',
        'placeholder_text'   => 'italic text-gray-400',
        'tag_bg'             => 'bg-blue-100',
        'tag_text'           => 'text-blue-700',
        'tag_hover_text'     => 'hover:text-blue-900',
        'footer_border'      => 'border-gray-100',
        'footer_bg'          => 'bg-white',
        'footer_text'        => 'text-gray-400',
        'empty_border'       => 'border-gray-200',
        'empty_bg'           => 'bg-white',
        'empty_text'         => 'text-gray-500',
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
