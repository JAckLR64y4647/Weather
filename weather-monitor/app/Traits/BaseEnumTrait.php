<?php

namespace App\Traits;

trait BaseEnumTrait
{
    public static function fromValue($value): static
    {
        foreach (self::cases() as $val) {
            if ($val->value === $value) {
                return $val;
            }
        }

        throw new \InvalidArgumentException("Value $value is not a valid constant in " . static::class);
    }
}
