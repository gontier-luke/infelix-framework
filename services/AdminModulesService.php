<?php

namespace Services;

use Classes\Router;
use Enum\LangEnum as Lang;
use Exceptions\ModuleException;
use Models\ModuleModel;
use Repositories\ModuleRepository;
/**
 * Service pour la gestion de l'interface d'administration des utilisateurs
 */
class AdminModulesService
{
    /**
     * Récupérer la liste des modules
     * @return array<ModuleModel> La liste des modules
     */
    private static function getModulesList(): array
    {
        $moduleRepo = new ModuleRepository();
        $modulesList = $moduleRepo->getAllModules();

        $modulesFiles = glob(BASE_PATH . 'modules/*/settings.xml');
        foreach ($modulesFiles as $file) {
            $settings = parseXmlFile($file);
            $moduleName = $settings['Module']['Name'];
            if($modulesList->filter(function($module) use ($moduleName) {
                return $module->getName() === $moduleName;
            })->count() > 0) {
                continue; // Le module existe déjà dans la base de données
            }

            $modulesList->add($moduleRepo->buildModule(
                null,
                $settings['Module']['Name'],
                $settings['Module']['Label'],
                $settings['Module']['Description'],
                $settings['Module']['Version'],
                false, // active
                false, // installed
                $settings['Module']['Author'],
                date('Y-m-d H:i:s'), // created_at
                date('Y-m-d H:i:s'),  // updated_at
                true // save
            ));
            // Ajouter le nouveau module à la liste des modules récupérés de la base de données
        }
        return $modulesList->toArray();
    }

    /**
     * Formater les données pour afficher la liste des modules
     * @return array Les données formatées pour l'affichage
     */
    public static function getModulesForDisplay(): array
    {
        $modules = self::getModulesList();
        $formattedModules = [];
        foreach ($modules as $module) {
            $actions = self::getActionsForModule($module->getId());
            if(!$module->isInstalled()) {
                unset($actions['enable']);
                unset($actions['disable']);
                unset($actions['désinstaller']);
            }
            $toggleUnset = $module->isActive() ? 'enable' : 'disable';
            $installUnset = $module->isInstalled() ? 'installer' : 'désinstaller';
            unset($actions[$installUnset]);
            unset($actions[$toggleUnset]);
            $formattedModules[] = [
                'name' => $module->getName(),
                'label' => $module->getLabel(),
                'description' => $module->getDescription(),
                'version' => $module->getVersion(),
                'actions' => $actions,
            ];
        }
        return $formattedModules;
    }

    /**
     * Récupérer le bouton pour créer un nouvel utilisateur administrateur
     * @return string Le code HTML du bouton
     */
    public static function getCreateModuleButton(): string
    {
        return createHTMLABalise(
            'createModule',
            Lang::trans('Créer un module', 'admin'),
            Router::generateUrl('app_admin_modules_create'),
            'btn'
        );
    }


    /**
     * Désactiver un module
     * @param int $id L'ID du module à désactiver
     * @return bool Indique si la désactivation a réussi
     */
    public static function disableModule(int $id): bool
    {
        $moduleRepo = new ModuleRepository();
        $moduleRepo->disableModule($id);
        return true;
    }

    public static function getActionsForModule($id): array
    {
        return [
            'disable' => [
                'label' => Lang::trans('Désactiver', 'admin'),
                'url' => Router::generateUrl('app_admin_module_disable', ['id' => $id])
            ],
            "enable" => [
                'label' => Lang::trans('Activer', 'admin'),
                'url' => Router::generateUrl('app_admin_module_enable', ['id' => $id])
            ],
            'configurer' => [
                'label' => Lang::trans('Configurer', 'admin'),
                'url' => Router::generateUrl('app_admin_module_configure', ['id' => $id])
            ],
            'installer' => [
                'label' => Lang::trans('Installer', 'admin'),
                'url' => Router::generateUrl('app_admin_module_install', ['id' => $id])
            ],
            'désinstaller' => [
                'label' => Lang::trans('Désinstaller', 'admin'),
                'url' => Router::generateUrl('app_admin_module_uninstall', ['id' => $id])
            ]
        ];
    }

    public static function installModule(string $moduleName): bool
    {
        $installPath = BASE_PATH . 'modules/' . $moduleName . '/install.php';
        if(!file_exists($installPath)) {
            throw new ModuleException("Le module '{$moduleName}' n'a pas pu être installé : il n'y a pas de fichier d'installation.");
        }

        try {
            require_once($installPath);
            $moduleRepo = new ModuleRepository();
            $module = $moduleRepo->findByName($moduleName);
            $module->setInstalled(true);
            $module->setActive(true);
            $module->update();

        } catch (\Exception $e) {
            throw new ModuleException("Erreur lors de l'installation du module '$moduleName': " . $e->getMessage());
        }

        return true;
    }

    public static function getModuleById(int $id): ?ModuleModel
    {
        $moduleRepo = new ModuleRepository();
        return $moduleRepo->findById($id);
    }

    public static function uninstallModule(string $moduleName): bool
    {
        $moduleRepo = new ModuleRepository();
        $module = $moduleRepo->findByName($moduleName);
        if(!$module) {
            throw new ModuleException("Le module '{$moduleName}' n'a pas été trouvé.");
        }

        try {
            $module->setInstalled(false);
            $module->setActive(false);
            $module->update();
        } catch (\Exception $e) {
            throw new ModuleException("Erreur lors de la désinstallation du module '$moduleName': " . $e->getMessage());
        }

        return true;
    }
}