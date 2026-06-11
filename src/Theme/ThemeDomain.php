<?php

declare(strict_types = 1);

namespace Brcas\Select\Theme;

class ThemeDomain
{
    public function __construct(
        protected \Closure $writer,
        protected string $prefix,
    ) {}

    public function border(string $value): static
    {
        ($this->writer)("{$this->prefix}_border", $value);

        return $this;
    }

    public function color(string $value): static
    {
        ($this->writer)("{$this->prefix}_color", $value);

        return $this;
    }

    public function padding(string $value): static
    {
        ($this->writer)("{$this->prefix}_padding", $value);

        return $this;
    }

    public function text(string $value): static
    {
        ($this->writer)("{$this->prefix}_text", $value);

        return $this;
    }

    public function extra(string $value): static
    {
        ($this->writer)("{$this->prefix}_extra", $value);

        return $this;
    }
}
