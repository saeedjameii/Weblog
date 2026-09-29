<?php

namespace App\Enums;

enum UserLevel: string {
    case User = 'user';
    case Admin = 'admin';
    case Creator = 'creator';
    public function label(): string{
        return match ($this){
            self::User => 'کاربر عادی',
            self::Admin => 'ادمین',
            self::Creator => 'سازنده',
        };
    }
}