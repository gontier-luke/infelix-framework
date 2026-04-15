<?php

namespace Repositories;

use Classes\ModelCore;
use Enum\CalendrierUserRoleEnum;
use Models\CalendrierUserModel;
use Exceptions\CalendrierUserException;
use \PDO;

class CalendrierRepository
{
    private static ?PDO $connection = null;

    // Implémentez les méthodes nécessaires pour interagir avec les utilisateurs administrateurs dans la base de données

    /**
     * Trouver un utilisateur administrateur par son nom d'utilisateur ou son adresse e-mail
     * @param string|null $username Le nom d'utilisateur de l'administrateur
     * @param string|null $email L'adresse e-mail de l'administrateur
     * @return CalendrierUserModel|null L'utilisateur administrateur trouvé ou null s'il n'existe pas
     */
    public function findByUsername(?string $username = null): ?CalendrierUserModel
    {
        // Logique pour trouver un utilisateur administrateur par son nom d'utilisateur
        if(is_null($username)) {
            // Aucun critère de connexion fourni
            return null;
        }
        if(is_null(self::$connection)) {
            self::$connection = CalendrierUserModel::connectBd();
        }

        $critere = 'username';
        $valeur = $username;
        $query = "SELECT id_calendrier_user, password FROM calendrier_user WHERE $critere = '$valeur'";
        try {
                $result = self::$connection->query($query);
        } catch (\Exception $e) {
            // Gérer l'exception si nécessaire
            throw new CalendrierUserException("Error while fetching configuration : " . self::$connection->errorInfo()[2]);
        }
        if(!$result) {
            throw new CalendrierUserException("Error while fetching configuration : " . self::$connection->errorInfo()[2]);
        }
        $config = $result->fetchObject();
        if(is_null($config) || $config === false) {
            return null;
        }


        return new CalendrierUserModel($config->id_calendrier_user);

    }

    public function verifyPassword(?string $username, string $password): bool
    {
        // Logique pour vérifier le mot de passe de l'utilisateur administrateur

        $calendrierUser = $this->findByUsername($username);
        if(is_null($calendrierUser)) {
            return false;
        }

        return password_verify($password, $calendrierUser->getPassword());
    }

    public function registerCalendrierUser(string $username, ?CalendrierUserRoleEnum $role, string $password): CalendrierUserModel
    {
        // Logique pour enregistrer un nouvel utilisateur administrateur
        if(!is_null($this->findByUsername($username))) {
            throw new CalendrierUserException("Un compte calendrier avec ce nom d'utilisateur existe déjà.");
        }

        $calendrierUser = new CalendrierUserModel();
        $calendrierUser->setUsername($username);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $calendrierUser->setPassword($hashedPassword);
        if(is_null($role)) {
            $role = CalendrierUserRoleEnum::JOUEUR;
        }
        $calendrierUser->setRole($role->value);
        $calendrierUser->insert();

        return $calendrierUser;
    }

    public function getUserRole(string $username): ?CalendrierUserRoleEnum
    {
        $calendrierUser = $this->findByUsername($username);
        if(is_null($calendrierUser)) {
            return null;
        }
        return $calendrierUser->getRole();
    }
}