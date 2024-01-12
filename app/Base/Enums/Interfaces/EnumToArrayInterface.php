<?php

namespace App\Base\Enums\Interfaces;

interface EnumToArrayInterface
{
    public static function names(): array;
    public static function values(): array;
    public static function fromName(string $name);
}
