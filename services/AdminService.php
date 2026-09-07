<?php

namespace Services;

use Classes\Router;
use Exceptions\AdminUserException;
use Repositories\AdminUserRepository;
use Repositories\Configuration;

/**
 * Service pour la gestion de l'interface d'administration
 */
class AdminService
{
    static private array $adminMenuLinks = [];

    /**
     * Vérifier si un administrateur est connecté sur cette session
     * @param array $session La session courante et ses données
     * @return bool Indique si l'administrateur est connecté
     */
    public static function isAdminLogged(array $session): bool
    {
        // Récupérer le token de session et vérifier s'il est valide

        $token = $session['admin_token'] ?? null;
        if(is_null($token)) {
            return false;
        }
        return self::isValidToken($token);
    }

    /**
     * Test de connexion de l'administrateur
     * @param string $username
     * @param string $password
     * @return bool Indique si la connexion a réussi
     * 
     * @throws AdminUserException
     */
    public static function login(string $username, string $password): bool
    {
        // Implémentez la logique de connexion de l'administrateur ici
        $email = null;
        if(self::isEmail($username)) {
            $email = $username;
            $username = null;
        }
        $adminUserRepo = new AdminUserRepository();
        if(!$adminUserRepo->verifyPassword($username, $email, $password)) {
            return false;
        }

        $_SESSION['admin_token'] = self::generateToken(session_id());
        $adminId = $adminUserRepo->getAdminUserIdByUsernameOrEmail($username, $email);
        if(!$adminId) {
            throw new AdminUserException("Impossible de récupérer l'ID de l'utilisateur administrateur après connexion.");
        }
        $_SESSION['admin_id'] = $adminId;

        return true;
    }

    public static function logout(): void
    {
        // Supprimez le token de session de l'administrateur
        unset($_SESSION['admin_token']);
    }

    private static function generateToken(string $sessionId): string
    {
        // Générer un token de session sécurisé

        // 1) Récupérer la date actuelle sous le format MMYYYYDD
        $date = new \DateTime();
        $formattedDate = $date->format('mYd');

        // 2) Diviser la journée en intervalles de 6 heures
        $interval = (int) ($date->format('H') / 6);
        $formattedInterval = sprintf('%02d', $interval);

        // 3) Combiner les éléments pour former le token
        $tokenString = $formattedDate . $formattedInterval;

        // 6) Ajouter une signature
        $tokenString .= 'poulpe'; // Clé secrète pour la signature

        // 7) Ajouter la session ID
        $tokenString .= $sessionId;

        // 8) Hacher le token avec la signature
        $hashedToken = hash('sha256', $tokenString);

        return $hashedToken;
    }

    
    private static function isValidToken(string $token): bool
    {
        // Valider le token de session

        $expectedToken = self::generateToken(session_id());
        return hash_equals($expectedToken, $token);
    }

    private static function isEmail(string $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Récupération du contenu du menu admin et le structure en fonction des id parents
     * @return array<string, string|array<string, string>> 
     */
    public static function getAdminMenuLinks(): array
    {
        self::$adminMenuLinks = array_merge([
                ['label' => 'Tableau de bord', 'url' => Router::generateUrl('app_admin_dashboard'), 'id' => 'dashboard'],
                ['label' => 'Utilisateurs Admin', 'url' => Router::generateUrl('app_admin_admin_users'), 'id' => 'users'],
                ['label' => 'Créer un utilisateur', 'url' => Router::generateUrl('app_admin_admin_users_create'), 'id' => 'users_create', 'parent' => 'users'],
                ['label' => 'Pages', 'url' => Router::generateUrl('app_admin_pages'), 'id' => 'pages'],
                ['label' => 'Créer une page', 'url' => Router::generateUrl('app_admin_pages_create'), 'id' => 'pages_create', 'parent' => 'pages'],
                ['label' => 'Modules', 'url' => Router::generateUrl('app_admin_modules'), 'id' => 'modules'],
                ['label' => 'Configurations', 'url' => Router::generateUrl('app_admin_config'), 'id' => 'configurations'],
                ['label' => 'Traductions', 'url' => Router::generateUrl('app_admin_translations'), 'id' => 'translations'],
                ['label' => 'Déconnexion', 'url' => Router::generateUrl('app_admin_logout'), 'id' => 'logout'],
            ], self::$adminMenuLinks
        );

        $useLangInUrl = Configuration::get('useLangInUrl');

        if($useLangInUrl){
            $adminMenuLinks[] = ['label' => 'Traductions', 'url' => Router::generateUrl('app_admin_translations'), 'id' => 'translations'];
        }

        $adminMenuLinks = [];
        foreach(self::$adminMenuLinks as $link) {
            if(key_exists('parent', $link) && !is_null($link['parent']) && $link['parent'] !== '') {
                // C'est un sous-menu
                $parentId = $link['parent'];
                if(!isset($adminMenuLinks[$parentId])) {
                    // Créer le parent s'il n'existe pas encore
                    $adminMenuLinks[$parentId] = [
                        'label' => '',
                        'url' => '#',
                        'children' => []
                    ];
                }
                $adminMenuLinks[$parentId]['children'][$link['id']] = [
                    'label' => $link['label'],
                    'url' => $link['url'],
                ];
                continue;
            } 
            // C'est un menu principal
            if(!key_exists($link['id'], $adminMenuLinks)) {
                $adminMenuLinks[$link['id']]['children'] = [];
            } 
            $adminMenuLinks[$link['id']]['label'] = $link['label'];
            $adminMenuLinks[$link['id']]['url'] = $link['url'] ?? '#';

        }

        
        return $adminMenuLinks;
    }

    /**
     * Ajouter un lien au menu admin
     * @param string $label
     * @param string $url
     * @param string $id
     * @param mixed $parent
     * @return void
     */
    public static function addAdminMenuLink(string $label, ?string $url, string $id, ?string $parent = null): bool
    {
        self::$adminMenuLinks[] = [
            'label' => $label,
            'url' => $url,
            'id' => $id,
            'parent' => $parent
        ];

        return true;
    }

    /**
     * Récupération des actions CRUD de modification en fonction du model et de son id
     * - Update
     * - Delete
     * @param string $modelClass juste le nom de la classe du model Ex : AdminUserModel => 'user'
     * @param string $id
     * @return array<string, array<string, string>>
     */
    public static function getCRUDEditActions(string $modelClass, string $id, ?array $extraAttributes = null): array
    {
        // app_admin_users_update
        $actions = [];
        $modelNameParts = explode('\\', $modelClass);
        // dump($modelNameParts);
        $modelName = end(array: $modelNameParts);
        // dump($modelName);
        $modelNameLower = camelToSnake(str_replace('Model', '', $modelName));

        $attributes = ['id' => $id];
        if($extraAttributes) {
            $attributes = array_merge($attributes, $extraAttributes);
        }

        // Update action
        $actions[] = createHTMLABalise("edit-{$modelNameLower}-{$id}", 'Modifier', Router::generateUrl("app_admin_{$modelNameLower}_update", $attributes), 'btn btn-primary');  
        // Delete action
        $actions[] = createHTMLABalise("delete-{$modelNameLower}-{$id}", 'Supprimer', Router::generateUrl("app_admin_{$modelNameLower}_delete", $attributes), 'btn btn-danger', [
            'data-confirm-message' => 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.'
        ]);
        return $actions;
    }
}