<?php

namespace Repositories;

use Models\AdminUserModel;
use Exceptions\AdminUserException;
use \PDO;

class AdminUserRepository
{
    private static ?PDO $connexion = null;

    // Implémentez les méthodes nécessaires pour interagir avec les utilisateurs administrateurs dans la base de données

    /**
     * Trouver un utilisateur administrateur par son nom d'utilisateur ou son adresse e-mail
     * @param string|null $username Le nom d'utilisateur de l'administrateur
     * @param string|null $email L'adresse e-mail de l'administrateur
     * @return AdminUserModel|null L'utilisateur administrateur trouvé ou null s'il n'existe pas
     */
    public function findByUsername(?string $username = null, ?string $email = null): ?AdminUserModel
    {
        // Logique pour trouver un utilisateur administrateur par son nom d'utilisateur
        if(is_null($username) && is_null($email)) {
            // Aucun critère de connexion fourni
            return null;
        }
        if(is_null(self::$connexion)) {
            self::$connexion = AdminUserModel::connectBd();
        }

        $critere = 'username';
        $valeur = $username;

        if(is_null($username)) {
            $critere = 'mail_address';
            $valeur = $email;
        }
        
        $query = "SELECT id_admin_user, password FROM admin_user WHERE $critere = '$valeur'";
        try {
                $result = self::$connexion->query($query);
        } catch (\Exception $e) {
            // Gérer l'exception si nécessaire
            throw new AdminUserException("Error while fetching configuration : " . self::$connexion->errorInfo()[2]);
        }
        if(!$result) {
            throw new AdminUserException("Error while fetching configuration : " . self::$connexion->errorInfo()[2]);
        }
        $config = $result->fetchObject();
        if(is_null($config) || $config === false) {
            return null;
        }


        return new AdminUserModel($config->id_admin_user);

    }

    public function verifyPassword(?string $username, ?string $email, string $password): bool
    {
        // Logique pour vérifier le mot de passe de l'utilisateur administrateur

        $adminUser = $this->findByUsername($username, $email);
        if(is_null($adminUser)) {
            return false;
        }

        return password_verify($password, $adminUser->getPassword());
    }

    public function registerAdminUser(string $username, string $mailAddress, string $password): AdminUserModel
    {
        // Logique pour enregistrer un nouvel utilisateur administrateur
        if(!is_null($this->findByUsername($username)) || !is_null($this->findByUsername(null, $mailAddress))) {
            throw new AdminUserException("Un compte administrateur avec ce nom d'utilisateur ou cette adresse e-mail existe déjà.");
        }

        $adminUser = new AdminUserModel();
        $adminUser->setUsername($username);
        $adminUser->setMailAddress($mailAddress);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $adminUser->setPassword($hashedPassword);
        $adminUser->insert();

        return $adminUser;
    }
}