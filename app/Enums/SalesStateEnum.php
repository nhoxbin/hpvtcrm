<?php

namespace App\Enums;

use App\Base\Enums\Interfaces\EnumToArrayInterface;
use App\Base\Enums\Traits\EnumToArrayTrait;

enum SalesStateEnum: string implements EnumToArrayInterface
{
    use EnumToArrayTrait;

    case NotAnswer = 'NotAnswer'; // 'Không nghe máy';
    case CannotContacted = 'CannotContacted'; // 'Thuê bao';
    case Busy = 'Busy'; // 'Bận hẹn gọi lại';
    case Registered = 'Registered'; // 'Đã đăng ký';
    case Called = 'Called'; // 'Đã gọi';

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
