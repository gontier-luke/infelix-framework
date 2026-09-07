<?php

namespace Services;

use Classes\Router;
use Enum\CalendrierGameEnum;
use Enum\CalendrierUserRoleEnum;
use Exceptions\CalendrierServiceException;
use Exceptions\CalendrierUserException;
use Repositories\CalendrierRepository;

/**
 * Service pour la gestion de l'interface d'administration
 */
class CalendrierService
{
    /**
     * liste des dates et des jeux associés
     * @var array<int, array<string,CalendrierGameEnum|array<string,mixed>>>
     */
    static private array $dates = [
        1 => [ 'game' => CalendrierGameEnum::PIOUPIOU, 'params' => [] ],
        2 => [ 'game' => CalendrierGameEnum::DESSINS, 'params' => ['folder' => 'day1'] ],
        3 => [ 'game' => CalendrierGameEnum::KARAOKE, 'params' => ['folder' => 'day1'] ],
        // 4 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 5 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 6 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 7 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 8 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 9 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 10 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 11 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 12 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 13 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 14 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 15 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 16 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 17 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 18 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 19 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 20 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 21 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 22 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 23 => [ 'game' => CalendrierGameEnum::, 'params' => [] ],
        // 24 => [ 'game' => CalendrierGameEnum::, 'params' => [] ]
    ];

    /**
     * Vérifier si un utilisateur est connecté sur cette session
     * @param array $session La session courante et ses données
     * @return bool Indique si l'utilisateur est connecté
     */
    public static function isUserLogged(array $session): bool
    {
        // Récupérer le token de session et vérifier s'il est valide

        $token = $session['calendrier_token'] ?? null;
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
     * @throws CalendrierServiceException
     */
    public static function login(string $username, string $password): bool
    {
        // Implémentez la logique de connexion de l'administrateur ici
        $calendrierRepo = new CalendrierRepository();
        if(!$calendrierRepo->verifyPassword($username, $password)) {
            return false;
        }

        $_SESSION['calendrier_token'] = self::generateToken(session_id());
        $_SESSION['calendrier_user_role'] = $calendrierRepo->getUserRole($username);

        return true;
    }

    public static function logout(): void
    {
        // Supprimez le token de session de l'administrateur
        unset($_SESSION['calendrier_token']);
        unset($_SESSION['calendrier_user_role']);
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
        $tokenString .= 'calendrier'; // Clé secrète pour la signature

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

    public static function registerUser(string $username, ?CalendrierUserRoleEnum $role, string $password): bool
    {
        $calendrierRepo = new CalendrierRepository();
        try {
            $calendrierRepo->registerCalendrierUser($username, $role, $password);
        } catch (CalendrierUserException $e) {
            // Gérer l'exception si nécessaire
            throw new CalendrierUserException("Erreur lors de l'enregistrement de l'utilisateur : " . $e->getMessage());
        }
        return true;
    }

    public static function getGameByDate(int $day): CalendrierGameEnum
    {
        $games = CalendrierGameEnum::cases();
        $index = ($day - 1) % count($games);
        return $games[$index];
    }

    private static function getGameForCurrentDay(): CalendrierGameEnum
    {
        $currentDay = (int) (new \DateTime())->format('d');
        return self::getGameByDate($currentDay);
    }

    public static function getFirstAccessToGame(CalendrierGameEnum $game): ?int
    {
        return array_search($game, self::$dates);
    }

    public static function verifyGameAccess(CalendrierGameEnum $game): bool
    {
        if(isset($_SESSION['calendrier_user_role']) && $_SESSION['calendrier_user_role'] === CalendrierUserRoleEnum::BETA_TEST) {
            return true;
        }
        $firstAccess = self::getFirstAccessToGame($game);
        $currentDay = (int) (new \DateTime())->format('d');
        return $currentDay >= $firstAccess;
    }

    public static function unlockHistoireHeroChapter(int $chapter): bool
    {
        if(!self::verifyGameAccess(CalendrierGameEnum::HISTOIREHERO)) {
            return false;
        }
        if($_SESSION){
            dd($_SESSION);
        }

        $calendrierRepo = new CalendrierRepository();
        return $calendrierRepo->unlockHistoireHeroChapter($chapter);
    }

    public static function getARandomModelToDraw(int $day): string
    {
        if(!self::verifyGameAccess(CalendrierGameEnum::DESSINS)) {
            throw new CalendrierServiceException("Accès au jeu Dessins non autorisé.");
        }
        $dayPassed = array_filter(self::$dates, function($d) use ($day) {
            return $d <= $day;
        }, ARRAY_FILTER_USE_KEY);
        $availableDessins = array_filter($dayPassed, function($actualDayData) {
            if(!key_exists('game', $actualDayData) && !key_exists('params', $actualDayData) && !key_exists('folder', $actualDayData['params'])) {
                return false;
            }
            return $actualDayData['game'] === CalendrierGameEnum::DESSINS;
        });
        if(empty($availableDessins)){
            throw new CalendrierServiceException("Aucun modèle disponible pour le jour spécifié.");
        }
        $folders = [];
        foreach($availableDessins as $dayDatas) {
            $folders[] = $dayDatas['params']['folder'];
        }
        $allModels = [];
        foreach($folders as $folder) {
            $allModels = array_merge($allModels, AssetsService::getAllImagesRelativeInFolder('dessineAvecMoi/' . $folder));
        }
        $selectedModel = $allModels[array_rand($allModels)];
        return Router::generateUrl('app_media_image', ['path' => $selectedModel['name'], 'extension' => $selectedModel['extension']]);
    }

    public static function getKaraokeVideoAndAudioRandom(int $day): array
    {
        if(!self::verifyGameAccess(CalendrierGameEnum::KARAOKE)) {
            throw new CalendrierServiceException("Accès au jeu KARAOKE non autorisé.");
        }
        $dayPassed = array_filter(self::$dates, function($d) use ($day) {
            return $d <= $day;
        }, ARRAY_FILTER_USE_KEY);

        $availableKaraokeVideos = array_filter($dayPassed, function($actualDayData) {
            if(!key_exists('game', $actualDayData) && !key_exists('params', $actualDayData) && !key_exists('folder', $actualDayData['params'])) {
                return false;
            }
            return $actualDayData['game'] === CalendrierGameEnum::KARAOKE;
        });

        if(empty($availableKaraokeVideos)){
            return ['video' => null, 'audio' => null];
        }
        $folders = [];
        foreach($availableKaraokeVideos as $dayDatas) {
            $folders[] = $dayDatas['params']['folder'];
        }
        $karaokeVideos = [];
        foreach($folders as $folder) {
            $karaokeVideos = array_merge($karaokeVideos, AssetsService::getAllVideosRelativeInFolder('karaoke/' . $folder));
        }
        $selectedKaraoke = $karaokeVideos[array_rand($karaokeVideos)];
        $videoURL = Router::generateUrl('app_media_video', ['path' => $selectedKaraoke['name'], 'extension' => $selectedKaraoke['extension']]);
        $audioURL = Router::generateUrl('app_media_audio', ['path' => $selectedKaraoke['name'], 'extension' => 'mp3']);
        return ['video' => $videoURL, 'audio' => $audioURL];
    }
}