<?php

namespace Repositories;

use Classes\ModelCore;
use Models\PageModel;
use Exceptions\PagesException;
use \PDO;

class PageRepository
{
    private static ?PDO $connection = null;

    // Implémentez les méthodes nécessaires pour interagir avec les pages dans la base de données

    /**
     * Trouver une page par son nom d'application
     * @param string|null $appName Le nom d'application de la page
     * @return PageModel|null La page trouvée ou null s'elle n'existe pas
     */
    static public function findByAppName(?string $appName = null): ?PageModel
    {
        // Logique pour trouver une page par son nom d'application
        if(is_null($appName)) {
            // Aucun critère de connexion fourni
            return null;
        }
        if(is_null(self::$connection)) {
            self::$connection = ModelCore::getConnection();
        }

        $critere = 'app_name';
        $valeur = $appName;
        $query = "SELECT `id_page` FROM `page` WHERE `$critere` = '$valeur'";
        try {
                $result = self::$connection->query($query);
        } catch (\Exception $e) {
            // Gérer l'exception si nécessaire
            throw new PagesException("Error while fetching configuration : " . self::$connection->errorInfo()[2]);
        }
        if(!$result) {
            throw new PagesException("Error while fetching configuration : " . self::$connection->errorInfo()[2]);
        }
        $config = $result->fetchObject();
        if(is_null($config) || $config === false) {
            return null;
        }


        return new PageModel($config->id_page);

    }

    public function getAllPages(): array
    {
        if(is_null(self::$connection)) {
            self::$connection = ModelCore::getConnection();
        }

        $query = "SELECT id_page FROM page";
        try {
                $result = self::$connection->query($query);
        } catch (\Exception $e) {
            // Gérer l'exception si nécessaire
            throw new PagesException("Error while fetching pages : " . self::$connection->errorInfo()[2]);
        }
        if(!$result) {
            throw new PagesException("Error while fetching pages : " . self::$connection->errorInfo()[2]);
        }
        $pages = [];
        while($row = $result->fetchObject()) {
            $pages[] = new PageModel($row->id_page);
        }

        return $pages;
    }

    public function getPagesById(int $id): PageModel
    {
        return new PageModel($id);
    }

    public function getPagesIdByAppName(?string $appName): ?int
    {
        $page = $this->findByAppName($appName);
        if(is_null($page)) {
            return null;
        }
        return $page->getId();
    }
}