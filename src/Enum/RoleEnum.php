<?php

namespace App\Enum;

enum RoleEnum: string
{
    case CONDUCTEUR = 'conducteur';
    case PASSAGER = 'passager';
    case ADMIN = 'admin';
}
