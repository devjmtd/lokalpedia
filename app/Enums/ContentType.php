<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ContentType: string implements HasLabel, HasIcon
{
    case Article = 'article';
    case Video = 'video';
    case Image = 'image';
    case Link = 'link';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Article => 'Article',
            self::Video => 'Video',
            self::Image => 'Image',
            self::Link => 'Link',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Article => 'heroicon-o-newspaper',
            self::Video => 'heroicon-o-video-camera',
            self::Image => 'heroicon-o-image',
            self::Link => 'heroicon-o-link',
        };
    }
}