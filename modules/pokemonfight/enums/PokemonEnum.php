<?php

namespace Modules\PokemonFight\Enums;

use Modules\PokemonFight\Enums\EffectCaseEnum;
use Modules\PokemonFight\Enums\EffectConditionEnum;
use Modules\PokemonFight\Enums\MoveEnum;

enum PokemonEnum: string
{
    case PIGEON = 'Pigeon';

    /** @var self */
    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;
        return $this;
    }
    /** @var string */
    public function getNickname(): string
    {
        return $this->nickname ?? $this->value;
    }

    /** @var self */
    public function setMoves(MoveEnum... $moves): self
    {
        if (count($moves) > 4) {
            # TODO: throw an exception
        }
        $this->moves = $moves;
        return $this;
    }
    
    /** @var MoveEnum[] */
    public function getMoves(): array
    {
        return $this->moves ?? [];
    }

    /** @var self */
    public function setCondition(PokemonConditionEnum... $conditions): self
    {
        $this->conditions = $conditions;
        return $this;
    }
    /** @var PokemonConditionEnum[] */
    public function getConditions(): array
    {
        return $this->conditions ?? [];
    }

    /** @var int */
    public function setLevel(int $level): self
    {
        $this->level = $level;
        return $this;
    }

    /** @var int */
    public function getLevel(): int
    {
        return $this->level ?? 1;
    }

    /** @var string */
    public function _toString(): string
    {
        $movesStr = '';
        foreach ($this->getMoves() as $move) {
            $movesStr .= "\t- " . str_replace("\n", "\n\t", $move->_toString()) . "\n";
        }
        return '[' . $this->nickname ?? $this->value . "]\n" . $movesStr . "\tConditions: " . implode(', ', array_map(fn($c) => $c->value, $this->getConditions())) . "\n\tLevel: " . $this->getLevel();
    }
}