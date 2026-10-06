<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Exceptions\BarionLocaleException;

enum Locale: string
{
    case Cz = 'cs-CZ';
    case En = 'en-US';
    case Es = 'es-ES';
    case De = 'de-DE';
    case Fr = 'fr-FR';
    case Hu = 'hu-HU';
    case Sk = 'sk-SK';
    case Sl = 'sl-SI';
    case Gr = 'el-GR';
    case Hr = 'hr-HR';
    case Ro = 'ro-RO';
    case Pl = 'pl-PL';
    case It = 'it-IT';
    case Bg = 'bg-BG';

    public static function getLocaleFromIso2(string $iso2): Locale
    {
        return match (strtolower($iso2)) {
            'cz', 'cs' => Locale::Cz,
            'en' => Locale::En,
            'es' => Locale::Es,
            'de' => Locale::De,
            'fr' => Locale::Fr,
            'hu' => Locale::Hu,
            'sk' => Locale::Sk,
            'sl' => Locale::Sl,
            'gr', 'el' => Locale::Gr,
            'hr' => Locale::Hr,
            'ro' => Locale::Ro,
            'pl' => Locale::Pl,
            'it' => Locale::It,
            'bg' => Locale::Bg,
            default => throw new BarionLocaleException('Locale not found'),
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
