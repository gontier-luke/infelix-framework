<?php

namespace Repositories;

use \PDO;
use Exceptions\FormationException;
use Models\FormationModel;
use Classes\Collections\EntityCollection;

class FormationRepository{

    private static ?PDO $connection = null;

    public static function get(string $name): FormationModel
    {
        if(is_null(self::$connection)) {
            self::$connection = FormationModel::getConnection();
        }

        $query = "SELECT id_formation FROM formation WHERE name = '$name'";
        $result = self::$connection->query($query);
        if(!$result) {
            throw new FormationException("Error while fetching formation : " . self::$connection->errorInfo()[2]);
        }
        $config = $result->fetchObject();
        if(is_null($config)) {
            throw new FormationException('Formation "'. $name .'" not found');
        }

        $FormationModel = new FormationModel($config->id_formation);
        return $FormationModel;
    }

    public static function getAll(): EntityCollection
    {
        if(is_null(self::$connection)) {
            self::$connection = FormationModel::getConnection();
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