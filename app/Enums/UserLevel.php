<?php

namespace App\Enums;

enum UserLevel: string {
    case User = 'user';
    case Creator = 'creator';
    public function label(): string{
        return match ($this){
            self::User => 'کاربر عادی',
            self::Creator => 'سازنده',
        };
    }
}