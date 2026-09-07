<?php

namespace Modules\PokemonFight\Enums;

enum EffectCaseEnum: string
{
    case DAMAGED = 'Damaged';
    case SEXISM = 'Opposite gender';
    case HELD_ITEM = 'Held item';
    case EACH_TURN = 'Heal each turn';

    /** @var self */
    public function setNot(): self
    {
        $this->value = '(Not) ' . $this->value;
        return $this;
    }
}