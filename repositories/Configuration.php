<?php

namespace Repositories;
use \PDO;
use Classes\ModelCore;
use Exceptions\ConfigException;
use Models\ConfigurationModel;

class Configuration{

    private static ?PDO $connection = null;

    public static function get(string $name, bool $required = true, string $default = ''): string
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
        if(is_null($config) || $config === false) {
            if($required) {
                throw new ConfigException('Configuration "'. $name .'" not found');
            }
            return $default;
        }

        $configModel = new ConfigurationModel($config->id_configuration);
        return $configModel->getValue();
    }

    public static function set(string $name, string $value): void
    { 
        if(is_null(self::$connection)) {
            self::$connection = ModelCore::getConnection();
        }

        $query = "SELECT id_configuration FROM configuration WHERE name = '$name'";
        $result = self::$connection->query($query);
        if(!$result) {
            dump(__FILE__, __LINE__, self::$connection->errorInfo());
            self::add($name, $value);
            return;
        }
        $config = $result->fetchObject();
        if(is_null($config) || $config === false) {
            dump(__FILE__, __LINE__, $config, $name);
            self::add($name, $value);
            return;
        }
        $configModel = new ConfigurationModel($config->id_configuration);
        $configModel->setValue($value);
        $configModel->update();
    }

    public static function add(string $name, string $value): void
    {
        if(is_null(self::$connection)) {
            self::$connection = ModelCore::getConnection();
        }

        $query = "INSERT INTO configuration (name, value) VALUES ('$name', '$value')";
        $result = self::$connection->query($query);
        if(!$result) {
            throw new ConfigException("Error while adding configuration : " . self::$connection->errorInfo()[2]);
        }
    }
}