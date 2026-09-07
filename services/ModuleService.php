<?php

namespace Services;

use Exceptions\ModuleException;
use Models\ModuleModel as Module;
use Repositories\ModuleRepository;

class ModuleService {
    private $moduleRepository;

    public function __construct() {
        $this->moduleRepository = new ModuleRepository();
    }

    public function createModule(array $data): Module {

        if($this->isModuleInstalled($data['name'])) {
            throw new ModuleException("Le module '{$data['name']}' est déjà installé.");
        }

        $module = $this->moduleRepository->buildModule(
            null,
            $data['name'],
            $data['label'],
            $data['description'],
            $data['version'],
            $data['active'],
            $data['installed'],
            $data['author'],
            $data['created_at'],
            $data['updated_at'],
            true // save
        );

        return $module;
    }

    public function installModule(array $settings): void {
        $this->createModule($settings);
    }

    public function isModuleActive(string $moduleName): bool {
        // Logique pour vérifier si le module est actif
        $module = $this->moduleRepository->findByName($moduleName);
        if ($module === null) {
            throw new ModuleException("Le module '{$moduleName}' n'existe pas.");
        }
        if (!$module->isActive()) {
            throw new ModuleException("Le module '{$moduleName}' n'est pas actif.");
        }
        return true; // Exemple simplifié
    }

    public function isModuleInstalled(string $moduleName): bool {
        // Logique pour vérifier si le module est installé
        $module = $this->moduleRepository->findByName($moduleName);
        return $module !== null;
    }

    public static function getActiveModules(): array {
        $moduleRepository = new ModuleRepository();
        return $moduleRepository->getAllActiveModules()->toArray();
    }

    public static function getModuleByName(string $moduleName): ?Module {
        $moduleRepository = new ModuleRepository();
        return $moduleRepository->findByName($moduleName);
    }
}