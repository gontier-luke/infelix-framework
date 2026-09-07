<?php

namespace Modules\PokemonFight\Enums;

enum MoveEnum: string
{
    case COUP_DELLE = 'Coup d\'Elle';

    /** @var self */
    public function setEffects(EffectEnum... $effectEnum): self
    {
        $this->effects = $effectEnum;
        return $this;
    }
        
    /** @var self */
    public function setPower(int $power): self
    {
        $this->power = $power;
        return $this;
    }
            
    public static function setUp(self $move): self
    {
        match ($move) {
            self::COUP_DELLE => $move
                ->setPower(50)
                ->setEffects(
                    EffectEnum::RAISE->setStats(EffectStatEnums::DODGE)->setCase(EffectCaseEnum::DAMAGED),
                    EffectEnum::APPLY->setCondition(EffectConditionEnum::PARALYZE)
                )
                ->setProb(50)
            ,
        };
        return $move;
    }
    /** @var self */
    public function setAccuracy(int $accuracy): self
    {
        $this->accuracy = $accuracy;
        return $this;
    }

    /** @var self */
    public function setMaxPP(int $maxPp, bool $isMax = true): self
    {
        $this->maxPp = $maxPp;
        if ($isMax) {
            $this->pp = $maxPp;
        }
        return $this;
    }
    /** @var self */
    public function setPP(int $pp): self
    {
        $this->pp = $pp;
        return $this;
    }

    /** @var self */
    public function setProb(int $prob): self
    {
        $this->prob = $prob;
        return $this;
    }

    public function getProb(): int
    {
        return $this->prob ?? 100;
    }

    public function useMove(): bool
    {
        if ($this->pp > 0) {
            $this->pp--;
            if ($this->pp < 0) {
                $this->pp = 0;
            }
            if ($this->pp === 0) {
                return $this->usable = false;
            }
        }
        return true;
    }

    public function isUsable(): bool
    {
        return $this->usable ?? true;
    }

    public function _toString(): string
    {
        return '[' . $this->value . "]\n\t" .
            'Power: ' . ($this->power ?? 'N/A') . "\n\t" .
            'Accuracy: ' . ($this->accuracy ?? 'N/A') . "\n\t" .
            'PP: ' . ($this->pp ?? 'N/A') . '/' . ($this->maxPp ?? 'N/A') . "\n\t" .
            'Effects: ' . (isset($this->effects) ? implode(', ', array_map(fn($effect) => $effect->_toString(), $this->effects)) : 'None') . "\n\t" .
            'Probability: ' . $this->getProb() . '%';
    }
}