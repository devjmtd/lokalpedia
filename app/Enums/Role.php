<?php

declare(strict_types=1);


namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Editor => 'Editor',
            self::Viewer => 'Viewer',
        };
    }
}
