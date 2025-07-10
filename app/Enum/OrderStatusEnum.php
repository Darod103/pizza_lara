<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

}
