<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Exceptions\BarionLocaleException;

enum Locale: string
{
    case Cz = 'cs-CZ';
    Case En = 'en-US';
    case Es = 'es-ES';
    case De = 'de-DE';
    case Fr = 'fr-FR';
    case Hu = 'hu-HU';
    case Sk = 'sk-SK';

    public static function getLocaleFromIso2(string $iso2): Locale
    {
        return match(strtolower($iso2)) {
            'cz' => Locale::Cz,
            'en' => Locale::En,
            'es' => Locale::Es,
            'de' => Locale::De,
            'fr' => Locale::Fr,
            'hu' => Locale::Hu,
            'sk' => Locale::Sk,
            default => throw new BarionLocaleException('Locale not found'),
        };
    }

    public function getValue(): string
    {
        return $this->value;
    }
}