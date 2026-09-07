<?php

namespace Modules\PokemonFight\Entities;

use Modules\PokemonFight\Enums\MoveCategoryEnum;

class MoveEntity
{
    /** @var string */
    private string $name;
    /** @var MoveCategoryEnum $category */
    private MoveCategoryEnum $category;
    /** @var ?int $power */
    private ?int $power;
    /** @var ?int $accuracy */
    private ?int $accuracy;
    /** @var int $pp */
    private int $pp;
    
    
}