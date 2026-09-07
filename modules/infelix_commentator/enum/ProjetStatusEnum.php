<?php

namespace Enum;
enum ProjetStatusEnum: string
{
    case PUBLIE = 'publié';
    case EN_COURS = 'en cours';
    case ANNULE = 'annulé';
    case BETA_TEST = 'bêta test';
    case TERMINE = 'terminé';
}