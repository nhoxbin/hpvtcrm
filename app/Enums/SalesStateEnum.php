<?php

namespace App\Enums;

enum SalesStateEnum: string
{
    case not_answer = 'Không nghe máy';
    case cannot_contacted = 'Thuê bao';
    case busy = 'Bận hẹn gọi lại';
    case registered = 'Đã đăng ký';
    case called = 'Đã gọi';

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
