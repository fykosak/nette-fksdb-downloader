<?php

declare(strict_types=1);

namespace Fykosak\NetteFKSDBDownloader\ORM\Models;

class ModelSchedule
{
    public int $eventId;
    public int $scheduleGroupId;
    public string $scheduleGroupType;

    public \DateTimeImmutable $end;
    public \DateTimeImmutable $start;

    /**
     * @var string[]
     */
    public array $name;

    /**
     * @var ModelScheduleItem[]
     */
    public array $scheduleItems;

    /**
     * @param ModelScheduleItem[] $scheduleItems
     * @internal item name is "schedule_items" therefore the mapping function is needed
     */
    public function setScheduleItems(array $scheduleItems)
    {
        $this->scheduleItems = $scheduleItems;
    }
}
