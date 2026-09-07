<?php

namespace Repositories;

use \PDO;
use Classes\ModelCore;
use Classes\Collections\ObjectCollection;
use Exceptions\ModuleException;
use Models\ModuleModel;

require_once BASE_PATH . 'models/ModuleModel.php';

class ModuleRepository {

    protected ?PDO $connection = null;

    // Méthodes pour interagir avec la base de données pour les modules

    public function __construct() {
        if(is_null($this->connection)) {
            $this->connection = ModelCore::getConnection();
        }
    }

    public function findByName(string $name): ModuleModel {
        if(is_null($this->connection)) {
            $this->connection = ModelCore::getConnection();
        }
        $query = "SELECT * FROM ". ModuleModel::$table ." WHERE name = :name";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':name', $name);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
            return $this->buildModule(
                $result['id_module'],
                $result['name'],
                $result['label'],
                $result['description'],
                $result['version'],
                $result['active'],
                $result['installed'],
                $result['author'],
                $result['created_at'],
                $result['updated_at']
            );
        }
        throw new ModuleException("Module '$name' not found.");
    }

    public function getAllActiveModules(): ObjectCollection {
        $query = "SELECT * FROM ". ModuleModel::$table ." WHERE active = 1 AND installed = 1";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        $modulesCollection = new ObjectCollection(ModuleModel::class);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $modulesCollection->add($this->buildModule(
                $row['id_module'],
                $row['name'],
                $row['label'],
                $row['description'],
                $row['version'],
                $row['active'],
                $row['installed'],
                $row['author'],
                $row['created_at'],
                $row['updated_at']
            ));
        }
        return $modulesCollection;
    }

    public function insert(ModuleModel $module): void {
        $module->insert();
    }

    public function disableModule(int $id): void {
        $module = new ModuleModel($id);
        $module->setActive(false);
        $module->update();
    }

    public function buildModule(int $id = null, ?string $name = null, ?string $label = null, ?string $description = null, ?string $version = null, ?bool $active = null, ?bool $installed = null, ?string $author = null, ?string $created_at = null, ?string $updated_at = null,?bool $save = false): ModuleModel {
        $newModule = (new ModuleModel())
            ->setName($name)
            ->setLabel($label)
            ->setDescription($description)
            ->setVersion($version)
            ->setActive($active)
            ->setInstalled($installed)  
            ->setAuthor($author)
            ->setCreatedAt($created_at)
            ->setUpdatedAt($updated_at);
        if ($id !== null) {
            $newModule->setId($id);
        }
        if ($save) {
            $newModule->insert();
        }
        return $newModule;
    }

    public function getAllModules(): ObjectCollection {
        $query = "SELECT * FROM ". ModuleModel::$table;
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        $modulesCollection = new ObjectCollection(ModuleModel::class);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $modulesCollection->add($this->buildModule(
                $row['id_module'],
                $row['name'],
                $row['label'],
                $row['description'],
                $row['version'],
                $row['active'],
                $row['installed'],
                $row['author'],
                $row['created_at'],
                $row['updated_at']
            ));
        }
        return $modulesCollection;
    }

    public function findById(int $id): ?ModuleModel {
        $query = "SELECT * FROM ". ModuleModel::$table ." WHERE id_module = :id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $this->buildModule(
                $result['id_module'],
                $result['name'],
                $result['label'],
                $result['description'],
                $result['version'],
                $result['active'],
                $result['installed'],
                $result['author'],
                $result['created_at'],
                $result['updated_at']
            );
        }
        return null;
    }
}