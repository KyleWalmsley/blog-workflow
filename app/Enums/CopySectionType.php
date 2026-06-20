<?php

namespace App\Enums;

enum CopySectionType: string
{
    case Banner = 'banner';
    case AboutUs = 'about_us';
    case AboutPage = 'about_page';
    case Service = 'service';
    case Meta = 'meta';

    public function label(): string
    {
        return match ($this) {
            self::Banner => 'Banner',
            self::AboutUs => 'About Us (Homepage)',
            self::AboutPage => 'About Page',
            self::Service => 'Service / Product',
            self::Meta => 'Meta Data',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            self::Banner => 0,
            self::AboutUs => 1,
            self::AboutPage => 2,
            self::Service => 3,
            self::Meta => 4,
        };
    }

    public function hasContent(): bool
    {
        return in_array($this, [self::AboutUs, self::AboutPage, self::Service]);
    }

    public function hasMeta(): bool
    {
        return in_array($this, [self::AboutPage, self::Service, self::Meta]);
    }

    public function hasBannerFields(): bool
    {
        return $this === self::Banner;
    }

    public function hasTitle(): bool
    {
        return in_array($this, [self::Service, self::Meta]);
    }
}
