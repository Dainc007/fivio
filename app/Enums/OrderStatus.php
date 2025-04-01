<?php

namespace App\Enums;

enum OrderStatus: string
{
    case ACTIVE = 'active';
    case FINISHED = 'finished';
    case CANCELLED = 'cancelled';

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::FINISHED => 'danger',
            self::CANCELLED => 'warning',
            default => 'gray',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('status.active'),
            self::FINISHED => __('status.finished'),
            self::CANCELLED => __('status.cancelled'),
        };
    }

    public static function withLabels(): array
    {
        return [
            self::ACTIVE->value => self::ACTIVE->label(),
            self::FINISHED->value => self::FINISHED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
        ];
    }
}
