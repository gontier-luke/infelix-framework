<?php

namespace Modules\PokemonFight\Enums;

enum MoveCategoryEnum: string
{
    case PHYSICAL = 'Physical';
    case SPECIAL = 'Special';
    case STATUS = 'Status';
}