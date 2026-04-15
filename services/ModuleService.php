<?php

namespace Services;

use Exceptions\ModuleException;
use Models\Module;
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

        $module = new Module(
            null,
            $data['name'],
            $data['label'],
            $data['description'],
            $data['version'],
            $data['active'],
            $data['author'],
            $data['created_at'],
        );
        $this->moduleRepository->insert(module: $module);

        return $module;
    }

    public function installModule(array $settings): void {
        $this->createModule($settings);
    }

    public function isModuleActive(string $moduleName): bool {
        // Logique pour vérifier si le module est actif
        return true; // Exemple simplifié
    }

    public function isModuleInstalled(string $moduleName): bool {
        // Logique pour vérifier si le module est installé
        $module = $this->moduleRepository->findByName($moduleName);
        return $module !== null;
    }

    public static function getActiveModules(): array {
        $moduleRepository = new ModuleRepository();
        return $moduleRepository->getAllActiveModules();
    }

    public static function getModuleByName(string $moduleName): ?Module {
        $moduleRepository = new ModuleRepository();
        return $moduleRepository->findByName($moduleName);
    }
}