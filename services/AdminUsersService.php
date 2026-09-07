<?php

namespace Services;

use Classes\FormBuilder;
use Classes\Router;
use Enum\InputTypeEnum;
use Enum\LangEnum as Lang;
use Models\AdminUserModel;
use Repositories\AdminUserRepository;
/**
 * Service pour la gestion de l'interface d'administration des utilisateurs
 */
class AdminUsersService
{
    /**
     * Récupérer la liste des utilisateurs administrateurs
     * @return array<AdminUserModel> La liste des utilisateurs administrateurs
     */
    private static function getAdminUsersList(): array
    {
        $adminUserRepo = new AdminUserRepository();
        return $adminUserRepo->getAllAdminUsers();
    }

    /**
     * Formater les données pour afficher la liste des utilisateurs administrateurs
     * @return array Les données formatées pour l'affichage
     */
    public static function getAdminUsersForDisplay(): array
    {
        $adminUsers = self::getAdminUsersList();
        $formattedUsers = [];
        foreach ($adminUsers as $adminUser) {
            $formattedUsers[] = [
                'username' => $adminUser->getUsername(),
                'email' => $adminUser->getMailAddress(),
                'actions' => AdminService::getCRUDEditActions('adminUsers', $adminUser->getId()),
            ];
        }
        return $formattedUsers;
    }

    /**
     * Récupérer le bouton pour créer un nouvel utilisateur administrateur
     * @return string Le code HTML du bouton
     */
    public static function getCreateAdminUserButton(): string
    {
        return createHTMLABalise(
            'createAdminUser',
            Lang::trans('Créer un utilisateur administrateur', 'admin'),
            Router::generateUrl('app_admin_admin_users_create'),
            'btn'
        );
    }

    /**
     * Générer le formulaire d'édition d'un utilisateur administrateur
     * @param ?int $id L'ID de l'utilisateur administrateur
     * @return string Le code HTML du formulaire
     */
    public static function getAdminUserForm(?int $id = null): string
    {
        $redirectAppName = 'app_admin_admin_users_create';
        $redirectParams = [];
        if($id) {
            $redirectAppName = 'app_admin_admin_users_update';
            $redirectParams = ['id' => $id];
        } 
        $user = new AdminUserModel($id);
        $formBuilder = (new FormBuilder())
            ->setFromModel($user)
            ->editInput(name:'username', type: InputTypeEnum::TEXT)
            ->editInput(name:'mail_address', type: InputTypeEnum::EMAIL)
            ->removeInput('password')
            ->add('submit', Lang::trans('Enregistrer', 'admin'), InputTypeEnum::SUBMIT)
            ->setAction(Router::generateUrl($redirectAppName, $redirectParams))
            ->setClass('crud-form')
            ; // On ne modifie pas le mot de passe ici
        return $formBuilder->renderForm();
    }

    /**
     * Supprimer un utilisateur administrateur
     * @return bool Indique si la suppression a réussi
     */
    public static function deleteAdminUser(int $id): bool
    {
        if(self::isItMe($id)) {
            throw new \Exception("Vous ne pouvez pas supprimer votre propre compte administrateur.");
        }

        $adminUser = new AdminUserModel($id);
        $adminUser->delete();
        return true;
    }

    /**
     * Vérifier si l'ID de l'utilisateur administrateur correspond à celui de la session courante
     * @param int $adminUserId
     * @return bool
     */
    public static function isItMe(int $adminUserId): bool
    {
        if(!isset($_SESSION['admin_id'])) {
            return false;
        }
        return $_SESSION['admin_id'] === $adminUserId;
    }

    /**
     * Mettre à jour un utilisateur administrateur
     * @param int $id L'ID de l'utilisateur à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool Indique si la mise à jour a réussi
     */
    public static function updateAdminUser(int $id, array $data): bool
    {
        $adminUser = new AdminUserModel($id);
        $adminUser->setUsername($data['username']);
        $adminUser->setMailAddress($data['mail_address']);
        return $adminUser->update();
    }

    /**
     * Créer un nouvel utilisateur administrateur
     * @param array $data Les données du nouvel utilisateur
     * @return bool Indique si la création a réussi
     */
    public static function createAdminUser(array $data): bool
    {
        $adminUser = new AdminUserModel();
        $adminUser->setUsername($data['username']);
        $adminUser->setMailAddress($data['mail_address']);
        $adminUser->setPassword('#a modifier#'); // Mot de passe par défaut à modifier
        $result = $adminUser->insert();

        if(!$result) {
            return false;
        }
        MailService::sendMail(
            $data['mail_address'],
            Lang::trans('Création de votre compte administrateur', 'admin'),
            TemplatesService::renderTemplate('services/mail/change_password_admin', [
                'username' => $data['username'],
                'newPasswordLink' => Router::generateUrl('app_admin_admin_users_reset_password_id', ['id' => $adminUser->getId()])
            ]),
        );
        return true;
    }

    /**
     * Summary of sendPasswordResetEmail
     * @param string $email
     * @return bool
     */
    public static function sendPasswordResetEmail(string $email): bool
    {
        $adminUserRepo = new AdminUserRepository();
        $adminUser = $adminUserRepo->getAdminUserIdByUsernameOrEmail(null, $email);
        if (!$adminUser) {
            return false;
        }
        // Générer un token de réinitialisation et l'envoyer par email
        return true;
    }
}