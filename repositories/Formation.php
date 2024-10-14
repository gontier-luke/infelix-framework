<?php

class Formation{

    private static ?PDO $connection = null;

    public static function get(string $name): FormationModel
    {
        if(is_null(self::$connection)) {
            self::$connection = ModelCore::getConnection();
        }

        $query = "SELECT id_configuration FROM configuration WHERE name = '$name'";
        $result = self::$connection->query($query);
        if(!$result) {
            throw new FormationException("Error while fetching configuration : " . self::$connection->errorInfo()[2]);
        }
        $config = $result->fetchObject();
        if(is_null($config)) {
            throw new FormationException('Configuration "'. $name .'" not found');
        }

        $formationModel = new FormationModel($config->id_configuration);
        return $formationModel;
    }

    public static function getAll(): EntityCollection
    {
        if(is_null(self::$connection)) {
            self::$connection = ModelCore::getConnection();
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