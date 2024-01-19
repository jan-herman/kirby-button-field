<?php

namespace JanHerman\ButtonField;

use Kirby\Cms\Url;

class Button
{
    private array $props;

    public function __construct(array $props)
    {
        $this->props = $props;
    }

    public function __call($name, $arguments = [])
    {
        return $this->props[$name] ?? null;
    }

    public function __toString()
    {
        return $this->url();
    }

    public function isEmpty(): bool
    {
        return empty($this->props['link']);
    }

    public function isNotEmpty(): bool
    {
        return $this->isEmpty() === false;
    }

    public function url(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        return Url::to($this->props['link']);
    }
}
