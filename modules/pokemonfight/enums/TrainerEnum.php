<?php

namespace Modules\PokemonFight\Enums;

enum TrainerEnum: string
{
    case ZARFIGNUM = 'Zarfignum';

    /** @var self */
    public function setPokemon(PokemonEnum... $pokemons): self
    {
        if (count($pokemons) > 6) {
            throw new \InvalidArgumentException('A trainer can have at most 6 Pokémon.');
        }
        $this->pokemons = $pokemons;
        return $this;
    }

    /** @var PokemonEnum[] */
    public function getPokemons(): array
    {
        return $this->pokemons ?? [];
    }

    /** @var self */
    public static function setUp(self $trainer): self
    {
        match ($trainer) {
            self::ZARFIGNUM =>
                $trainer->setPokemon(
                    PokemonEnum::PIGEON
                        ->setNickname('Oizo')
                        ->setMoves(MoveEnum::COUP_DELLE),
                    PokemonEnum::PIGEON
                        ->setNickname('Oizo 2')
                        ->setMoves(MoveEnum::COUP_DELLE),
                )
        };
        return $trainer;
    }

    public function _toString(): string
    {
        $pokemonsString = implode("\n", array_map(fn($pokemon) => $pokemon->_toString(), $this->getPokemons()));
        return '{' . $this->value . " with pokémons: \n\t". str_replace("\n", "\n\t", $pokemonsString) . '}';
    }
}