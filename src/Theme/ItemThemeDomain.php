<?php

declare(strict_types = 1);

namespace Brcas\Select\Theme;

final class ItemThemeDomain extends ThemeDomain
{
    public function hover(string $value): static
    {
        ($this->writer)('item_hover', $value);

        return $this;
    }

    public function selected(string $value): static
    {
        ($this->writer)('item_selected', $value);

        return $this;
    }

    public function selectedIcon(string $value): static
    {
        ($this->writer)('item_selected_icon', $value);

        return $this;
    }
}
