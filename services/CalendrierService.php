<?php

namespace Services;

use Exceptions\CalendrierException;
use Repositories\CalendrierRepository;
use Exceptions\CalendrierUserException;
use Enum\CalendrierGameEnum;
use Enum\CalendrierUserRoleEnum;

/**
 * Service pour la gestion de l'interface d'administration
 */
class CalendrierService
{
    /**
     * liste des dates et des jeux associés
     * @var array<int, CalendrierGameEnum>
     */
    private array $dates = [
        1 => CalendrierGameEnum::PIOUPIOU,
        // 2 => CalendrierGameEnum::,
        // 3 => CalendrierGameEnum::,
        // 4 => CalendrierGameEnum::,
        // 5 => CalendrierGameEnum::,
        // 6 => CalendrierGameEnum::,
        // 7 => CalendrierGameEnum::,
        // 8 => CalendrierGameEnum::,
        // 9 => CalendrierGameEnum::,
        // 10 => CalendrierGameEnum::,
        // 11 => CalendrierGameEnum::,
        // 12 => CalendrierGameEnum::,
        // 13 => CalendrierGameEnum::,
        // 14 => CalendrierGameEnum::,
        // 15 => CalendrierGameEnum::,
        // 16 => CalendrierGameEnum::,
        // 17 => CalendrierGameEnum::,
        // 18 => CalendrierGameEnum::,
        // 19 => CalendrierGameEnum::,
        // 20 => CalendrierGameEnum::,
        // 21 => CalendrierGameEnum::,
        // 22 => CalendrierGameEnum::,
        // 23 => CalendrierGameEnum::,
        // 24 => CalendrierGameEnum::
    ];

    /**
     * Vérifier si un utilisateur est connecté sur cette session
     * @param array $session La session courante et ses données
     * @return bool Indique si l'utilisateur est connecté
     */
    public static function isUserLogged(array $session): bool
    {
        // Récupérer le token de session et vérifier s'il est valide

        $token = $session['calendrier_token'] ?? null;
        if(is_null($token)) {
            return false;
        }
        return self::isValidToken($token);
    }

    /**
     * Test de connexion de l'administrateur
     * @param string $username
     * @param string $password
     * @return bool Indique si la connexion a réussi
     * 
     * @throws CalendrierException
     */
    public static function login(string $username, string $password): bool
    {
        // Implémentez la logique de connexion de l'administrateur ici
        $calendrierRepo = new CalendrierRepository();
        if(!$calendrierRepo->verifyPassword($username, $password)) {
            return false;
        }

        $_SESSION['calendrier_token'] = self::generateToken(session_id());
        $_SESSION['calendrier_user_role'] = $calendrierRepo->getUserRole($username);

        return true;
    }

    public static function logout(): void
    {
        // Supprimez le token de session de l'administrateur
        unset($_SESSION['calendrier_token']);
        unset($_SESSION['calendrier_user_role']);
    }

    private static function generateToken(string $sessionId): string
    {
        // Générer un token de session sécurisé

        // 1) Récupérer la date actuelle sous le format MMYYYYDD
        $date = new \DateTime();
        $formattedDate = $date->format('mYd');

        // 2) Diviser la journée en intervalles de 6 heures
        $interval = (int) ($date->format('H') / 6);
        $formattedInterval = sprintf('%02d', $interval);

        // 3) Combiner les éléments pour former le token
        $tokenString = $formattedDate . $formattedInterval;

        // 6) Ajouter une signature
        $tokenString .= 'calendrier'; // Clé secrète pour la signature

        // 7) Ajouter la session ID
        $tokenString .= $sessionId;

        // 8) Hacher le token avec la signature
        $hashedToken = hash('sha256', $tokenString);

        return $hashedToken;
    }
    
    private static function isValidToken(string $token): bool
    {
        // Valider le token de session

        $expectedToken = self::generateToken(session_id());
        return hash_equals($expectedToken, $token);
    }

    public static function registerUser(string $username, ?CalendrierUserRoleEnum $role, string $password): bool
    {
        $calendrierRepo = new CalendrierRepository();
        try {
            $calendrierRepo->registerCalendrierUser($username, $role, $password);
        } catch (CalendrierUserException $e) {
            // Gérer l'exception si nécessaire
            throw new CalendrierUserException("Error while registering user : " . $e->getMessage());
        }
        return true;
    }

    public static function getGameByDate(int $day): CalendrierGameEnum
    {
        $games = CalendrierGameEnum::cases();
        $index = ($day - 1) % count($games);
        return $games[$index];
    }

    private static function getGameForCurrentDay(): CalendrierGameEnum
    {
        $currentDay = (int) (new \DateTime())->format('d');
        return self::getGameByDate($currentDay);
    }

    public static function getFirstAccessToGame(CalendrierGameEnum $game): ?int
    {
        return array_search($game, self::$dates);
    }

    public static function verifyGameAccess(CalendrierGameEnum $game): bool
    {
        if(isset($_SESSION['calendrier_user_role']) && $_SESSION['calendrier_user_role'] === CalendrierUserRoleEnum::BETA_TEST->value) {
            return true;
        }
        $firstAccess = self::getFirstAccessToGame($game);
        $currentDay = (int) (new \DateTime())->format('d');
        return $currentDay >= $firstAccess;
    }
}