<?php

declare(strict_types=1);

namespace Fykosak\NetteFKSDBDownloader\ORM\Services;

use Fykosak\FKSDBDownloaderCore\Requests\EventListRequest;
use Fykosak\NetteFKSDBDownloader\ORM\Models\ModelEvent;

final class ServiceEventList extends AbstractJSONService
{
    /**
     * @return ModelEvent[]
     */
    public function getEvents(array $eventTypeIds, ?string $explicitExpiration = null): array
    {
        $items = $this->getItem(
            new EventListRequest($eventTypeIds),
            [],
            ModelEvent::class,
            true,
            $explicitExpiration
        );
        usort($items, fn(ModelEvent $a, ModelEvent $b): int => $a->begin <=> $b->begin);
        return $items;
    }

    /**
     * @return ModelEvent[]
     */
    public function getEventsByYear(array $eventTypeIds, int $year, ?string $explicitExpiration = null): array
    {
        return array_filter(
            $this->getEvents($eventTypeIds, $explicitExpiration),
            fn(ModelEvent $event): bool => $year == $event->begin->format('Y')
        );
    }

    /**
     * @return ModelEvent
     */
    public function getNewest(array $eventTypeIds, ?string $explicitExpiration = null): ModelEvent
    {
        $events = $this->getEvents($eventTypeIds, $explicitExpiration);
        return end($events);
    }
}
