<?php

namespace App\Enum;

enum RoleEnum: string
{
    case ADMIN = 'ROLE_ADMIN';
    case CONDUCTEUR = 'ROLE_CONDUCTEUR';
    case PASSAGER = 'ROLE_PASSAGER';

    public function getLabel(): string
    {
        return match($this) {
            self::ADMIN => 'Administrateur',
            self::CONDUCTEUR => 'Conducteur',
            self::PASSAGER => 'Passager',
        };
    }
}
