<?php

namespace Services;

use Exceptions\AdminUserException;
use Repositories\AdminUserRepository;

/**
 * Service pour la gestion de l'interface d'administration
 */
class AdminService
{
    /**
     * Vérifier si un administrateur est connecté sur cette session
     * @param array $session La session courante et ses données
     * @return bool Indique si l'administrateur est connecté
     */
    public static function isAdminLogged(array $session): bool
    {
        // Récupérer le token de session et vérifier s'il est valide

        $token = $session['admin_token'] ?? null;
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
     * @throws AdminUserException
     */
    public static function login(string $username, string $password): bool
    {
        // Implémentez la logique de connexion de l'administrateur ici
        $email = null;
        if(self::isEmail($username)) {
            $email = $username;
            $username = null;
        }
        $adminUserRepo = new AdminUserRepository();
        if(!$adminUserRepo->verifyPassword($username, $email, $password)) {
            return false;
        }

        $_SESSION['admin_token'] = self::generateToken(session_id());

        return true;
    }

    public static function logout(): void
    {
        // Supprimez le token de session de l'administrateur
        unset($_SESSION['admin_token']);
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
        $tokenString .= 'poulpe'; // Clé secrète pour la signature

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

    private static function isEmail(string $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
    }
}