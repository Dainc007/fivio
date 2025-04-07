<?php

declare(strict_types=1);

namespace App\Enums;

enum OfferStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case ACCEPTED = 'accepted';

    public static function withLabels(): array
    {
        return [
            self::ACCEPTED->value => self::ACCEPTED->label(),
            self::PENDING->value => self::PENDING->label(),
            self::REJECTED->value => self::REJECTED->label(),
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'info',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
            default => 'gray',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('status.pending'),
            self::ACCEPTED => __('status.accepted'),
            self::REJECTED => __('status.rejected'),
        };
    }
}
