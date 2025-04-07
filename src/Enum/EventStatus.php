<?php

namespace App\Enum;

enum EventStatus: string
{
    case ANNULE = 'ANNULE';
    case RESOLU = 'RESOLU';
    case EN_COUR = 'EN_COUR';
} 