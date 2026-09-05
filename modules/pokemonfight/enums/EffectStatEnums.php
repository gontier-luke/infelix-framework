<?php

namespace Modules\PokemonFight\Enums;

enum EffectStatEnums: string
{
    case ATTACK = 'Attack';
    case DEFENSE = 'Defense';
    case SPECIAL_ATTACK = 'Special Attack';
    case SPECIAL_DEFENSE = 'Special Defense';
    case SPEED = 'Speed';
    case DAMAGE = 'Damage';
    case HP = 'HP';
    case ACCURACY = 'Accuracy';
    case EVASION = 'Evasiveness';
    case CRITICAL_HIT = 'Critical Hit Ratio';
    case ALL_STATS = 'All stats';
    case CURE_STATUS = 'Cure Status';
    case WEIGHT = 'Weight';
    case DODGE = 'Dodge';
    

    /** @var self */
    public function setSelfTargeted(): self
    {
        $this->value = '[Self-Targeted] ' . $this->value;
        return $this;
    }
    /** @var self */
    public function setPartyTargeted(): self
    {
        $this->value = '[Party-Targeted] ' . $this->value;
        return $this;
    }
    /** @var self */
    public function setCondition(EffectConditionEnum $condition): self
    {
        $this->value = '{' . $condition->value . '} ' . $this->value;
        return $this;
    }
}