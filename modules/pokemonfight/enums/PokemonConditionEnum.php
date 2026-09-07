<?php

namespace Modules\PokemonFight\Enums;

enum PokemonConditionEnum: string
{
    case PARALYZE = 'Paralyze';
    case SLEEP = 'Sleep';
    case BURN = 'Burn';
    case FREEZE = 'Freeze';
    case POISON = 'Poison';
    case CONFUSION = 'Confusion';
    case FLINCH = 'Flinch';

    /** @var self */
    public function setNot(): self
    {
        $this->value = '(Not) ' . $this->value;
        return $this;
    }
}