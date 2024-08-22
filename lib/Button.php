<?php

namespace JanHerman\ButtonField;

use Kirby\Cms\Url;
use Kirby\Toolkit\Str;

class Button
{
    private array $props;

    public function __construct(array $props)
    {
        $this->props = $props;
    }

    public function __call($name)
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

    public function target(): string
    {
        return $this->props['new_tab'] === 'true' ? '_blank' : '';
    }

    // based on:
    // https://github.com/getkirby/kirby/blob/v4/develop/panel/src/components/Forms/Field/LinkField.vue#L127
    // and https://github.com/tobimori/kirby-spielzeug/blob/main/config/fieldMethods.php
    public function type(): string
    {
        $link = $this->link();

        if (empty($link)) {
            return 'custom';
        }

        if (Str::match($link, '/^(http|https):\/\//')) {
            return 'url';
        }

        if (Str::startsWith($link, 'page://') || Str::startsWith($link, '/@/page/')) {
            return 'page';
        }

        if (Str::startsWith($link, 'file://') || Str::startsWith($link, '/@/file/')) {
            return 'file';
        }

        if (Str::startsWith($link, 'tel:')) {
            return 'tel';
        }

        if (Str::startsWith($link, 'mailto:')) {
            return 'email';
        }

        if (Str::startsWith($link, '#')) {
            return 'anchor';
        }

        return 'custom';
    }

    public function textFallback(): string
    {
        $link = $this->link();
        $type = $this->type();

        switch ($type) {
            case 'url':
                return Url::short($link);
            case 'page':
                $page = page($link);
                if ($page) {
                    return $page->title();
                }
                // no break
            case 'file':
                $file = kirby()->file($link);
                if ($file) {
                    return $file->filename();
                }
                // no break
            case 'email':
                return Str::replace($link, 'mailto:', '');
            case 'tel':
                return Str::replace($link, 'tel:', '');
            default:
                return $link;
        };
    }
}
