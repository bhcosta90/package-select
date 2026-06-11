<?php

declare(strict_types = 1);

namespace Brcas\Select\Theme;

final class InputThemeDomain extends ThemeDomain
{
    public function searchText(string $value): static
    {
        ($this->writer)('input_search_text', $value);

        return $this;
    }
}
