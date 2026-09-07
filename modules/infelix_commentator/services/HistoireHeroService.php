<?php

namespace Services;

use Entities\EventEntities;

/**
 * Service pour la gestion de l'interface d'administration
 */
class HistoireHeroService
{
    /** @var array<EventEntities> $events */
    private static $events = [];

    /**
     * Récupère les événements depuis le fichier JSON
     *
     * @return array<EventEntities>
     */
    public static function getEvents(): array
    {
        if (empty(self::$events)) {
            $json = file_get_contents(__DIR__ . '/../events.json');
            $data = json_decode($json, true);

            foreach ($data as $eventData) {
                $event = new EventEntities($eventData['id'], $eventData['name'], $eventData['content'], $eventData['images'], $eventData['parameters']  , $eventData['directRouting'] ?? [], $eventData['parametersRouting'] ?? []);

                self::$events[] = $event;
            }
        }

        return self::$events;
    }
}