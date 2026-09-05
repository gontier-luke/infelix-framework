<?php

namespace Modules\PokemonFight\Enums;

enum EffectEnum: string
{
    case APPLY = 'Apply';
    case LOWER = 'Lower';
    case RAISE = 'Raise';
    case RECOVER = 'Recover';
    case RECOVER_HP_HALF_DAMAGE = 'Recover HP Half Damage';
    case FIRST_ATTACK = 'First Attack';
    case STRONGER = 'Stronger';
    case RAISE_RANDOM_STAT = 'Raise a random stat';
    case IGNORE = 'Ignore';
    case HIGH = 'High';
    case SWITCH = 'Switch';
    case RAISE_ALL_STATS = 'Raise all stats';
    case LOWER_ALL_STATS = 'Lower all stats';
    case HIT_TIMES = 'Hit multiple times';
    case CURE_STATUS = 'Cure Status';
    case USE_ALLY_RANDOM_MOVE = 'Use a random move of an ally';
    case DOUBLE_POWER = 'Double power';
    case HIGHER_CRITICAL_HIT = 'Higher Critical Hit Ratio';
    case ATTACK_LESS = 'attack less';
    case HALVE_P_ATTACK = 'Halve physical attack';
    case HALVE_S_ATTACK = 'Halve special attack';
    case REDUCE_WEIGHT = 'Reduce weight';
    case INCREASE_WEIGHT = 'Increase weight';

    /** @var self */
    public function setTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }
    /** @var string */
    public function getTarget(): string
    {
        $target = $this->target ?? 'opponent';
        return $target;
    }


    /** @var self */
    public function setCase(EffectCaseEnum $case): self
    {
        $this->case = $case;
        return $this;
    }
    /** @var EffectCaseEnum */
    public function getCase(): EffectCaseEnum
    {
        return $this->case;
    }

    /** @var self */
    public function setCondition(EffectConditionEnum... $conditions): self
    {
        $this->conditions = $conditions;
        return $this;
    }
    /** @var EffectConditionEnum[] */
    public function getConditions(): array
    {
        return $this->conditions ?? [];
    }

    /** @var self */
    public function setStats(EffectStatEnums... $stats): self
    {
        $this->stats = $stats;
        return $this;
    }
    /** @var EffectStatEnums[] */
    public function getStats(): array
    {
        return $this->stats ?? [];
    }
}