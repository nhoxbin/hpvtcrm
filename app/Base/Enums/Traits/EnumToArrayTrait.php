<?php

namespace App\Base\Enums\Traits;

trait EnumToArrayTrait
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromName(string $name) {

        return constant("self::$name");
    }
}
