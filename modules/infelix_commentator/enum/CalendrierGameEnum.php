<?php

namespace Enum;
enum CalendrierGameEnum: string
{
    case PIOUPIOU = 'Flappy Pioupiou';
    case KARAOKE = 'Karaoké';
    case QUIZ = 'Quiz';
    case HISTOIREHERO = 'Histoire dont tu es le héros';
    case CLICKER = 'Luke Clicker';
    case OUESTALICE = 'Ou est Alice ?';
    case TROUS = 'Trous';
    case POKEMON = 'Combat Pokemon';
    case DESSINS = 'Concours de dessins';

    public function getAppName(): string
    {
        return 'app_' . $this->getSimpleName();
    }

    public function getImage(): string
    {
        return 'img_' . $this->getSimpleName();
    }

    public function getSimpleName(): string
    {
        return match($this) {
            self::PIOUPIOU => 'pioupiou',
            self::KARAOKE => 'karaoke',
            self::QUIZ => 'quiz',
            self::HISTOIREHERO => 'histoire_hero',
            self::CLICKER => 'clicker',
            self::OUESTALICE => 'ou_est_alice',
            self::TROUS => 'trous',
            self::POKEMON => 'pokemon',
            self::DESSINS => 'dessins',
        };
    }
}