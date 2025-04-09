<?php

namespace App\Entity;

enum RoleEnum: string
{
    case CONDUCTEUR = 'conducteur';
    case PASSAGER = 'passager';
    case ADMIN = 'admin';
}
