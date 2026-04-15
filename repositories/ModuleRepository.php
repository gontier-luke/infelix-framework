<?php

namespace Repositories;

use \PDO;
use Classes\ModelCore;
use Exceptions\ModuleException;
use Models\ModuleModel as Module;

require_once BASE_PATH . 'models/ModuleModel.php';

class ModuleRepository {

    private static ?PDO $connection = null;

    // Méthodes pour interagir avec la base de données pour les modules

    public function findByName(string $name): Module {
        if(is_null(self::$connection)) {
            self::$connection = Module::connectBd();
        }
        $query = "SELECT id_module, label, description, version, active, author, created_at FROM ". Module::$table ." WHERE name = :name";
        $stmt = self::$connection->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        dump($result);
        if ($result) {
            return new Module(
                $result['id_module'],
                $name,
                $result['label'],
                $result['description'],
                $result['version'],
                $result['active'],
                $result['author'],
                $result['created_at']
            );
        }
        throw new ModuleException("Module '$name' not found.");
    }

    public function getAllActiveModules(): array {
        if(is_null(self::$connection)) {
            self::$connection = Module::connectBd();
        }
        $query = "SELECT * FROM ". Module::$table ." WHERE active = 1";
        $stmt = self::$connection->prepare($query);
        $stmt->execute();

        $modules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $modules[] = new Module(
                $row['id_module'],
                $row['name'],
                $row['label'],
                $row['description'],
                $row['version'],
                $row['active'],
                $row['author'],
                $row['created_at']
            );
        }
        return $modules;
    }

    public function insert(Module $module): void {
        $module->insert();
    }

}