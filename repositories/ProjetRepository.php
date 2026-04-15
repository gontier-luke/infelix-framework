<?php

namespace Repositories;

use \PDO;
use Exceptions\ProjetException;
use Models\ProjetModel;
use Collections\EntityCollection;

class ProjetRepository{

    private static ?PDO $connection = null;

    public static function get(string $name): ProjetModel
    {
        if(is_null(self::$connection)) {
            self::$connection = ProjetModel::connectBd();
        }

        $query = "SELECT id_projet FROM projet WHERE name = '$name'";
        $result = self::$connection->query($query);
        if(!$result) {
            throw new ProjetException("Error while fetching projet : " . self::$connection->errorInfo()[2]);
        }
        $config = $result->fetchObject();
        if(is_null($config)) {
            throw new ProjetException('Projet "'. $name .'" not found');
        }

        $projetModel = new ProjetModel($config->id_projet);
        return $projetModel;
    }

    public static function getAll(): EntityCollection
    {
        if(is_null(self::$connection)) {
            self::$connection = ProjetModel::connectBd();
        }

        $query = "SELECT id_formation FROM formation";
        $result = self::$connection->query($query);
        if(!$result) {
            throw new FormationException("Error while fetching formations : " . self::$connection->errorInfo()[2]);
        }
        $formations = $result->fetchAll(PDO::FETCH_OBJ);
        $collection = new EntityCollection(FormationModel::class);
        foreach ($formations as $formation) {
            $collection->add(new FormationModel($formation->id_formation));
        }
        return $collection;
    }
}