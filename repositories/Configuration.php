<?php

class Configuration{

    private static ?PDO $connection = null;

    public static function get(string $name): string
    {
        if(is_null(self::$connection)) {
            self::$connection = ModelCore::getConnection();
        }

        $query = "SELECT id_configuration FROM configuration WHERE name = '$name'";
        $result = self::$connection->query($query);
        if(!$result) {
            throw new ConfigException("Error while fetching configuration : " . self::$connection->errorInfo()[2]);
        }
        $config = $result->fetchObject();
        if(is_null($config)) {
            throw new ConfigException('Configuration "'. $name .'" not found');
        }

        $configModel = new ConfigurationModel($config->id_configuration);
        return $configModel->getValue();
    }
}