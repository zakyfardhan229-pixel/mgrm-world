<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Transfer = 'transfer';
    case Cod = 'cod';
    case Qris = 'qris';

    /**
     * Allowed values, used by validation rules and seeders.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Bank Transfer',
            self::Cod => 'COD (Bayar di Tempat)',
            self::Qris => 'QRIS',
        };
    }
}
