<?php

namespace App\DTO;

class OrderStatusDTO
{

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }
}
