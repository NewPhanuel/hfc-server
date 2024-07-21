<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\Entity\EventEntity;
use RedBeanPHP\R;

final class EventModel
{
    private const TABLE_NAME = 'events';

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function store(EventEntity $eventEntity): int|string
    {
        $eventBean = R::dispense(self::TABLE_NAME);
        $eventBean['event_uuid'] = $eventEntity->getEventUuid();
        $eventBean['event_name'] = $eventEntity->getName();
        $eventBean['description'] = $eventEntity->getDescription();
        $eventBean['event_date'] = $eventEntity->getDate();
        $eventBean['event_image'] = $eventEntity->getImage();
        $eventBean['created_at'] = $eventEntity->getCreatedAt();
        $eventBean['updated_at'] = $eventEntity->getUpdatedAt();
        $beanId = R::store($eventBean);
        R::close();
        return $beanId;
    }

    public static function update(string $uuid, EventEntity $eventEntity): mixed
    {
        $eventBean = R::findOne(self::TABLE_NAME, 'event_uuid = ?', [$uuid]);

        if ($eventBean) {
            if ($eventEntity->getName())
                $eventBean['event_name'] = $eventEntity->getName();
            if ($eventEntity->getDescription())
                $eventBean['description'] = $eventEntity->getDescription();
            if ($eventEntity->getDate())
                $eventBean['event_date'] = $eventEntity->getDate();
            if ($eventEntity->getImage())
                $eventBean['event_image'] = $eventEntity->getImage();
            $eventBean['updated_at'] = $eventEntity->getUpdatedAt();
            R::store($eventBean);
            R::close();
            return $eventBean;
        }
        throw new InvalidValidationException('Invalid Event UUID');
    }

    public static function show(string $uuid): array
    {
        $eventBean = R::findOne(self::TABLE_NAME, 'event_uuid = ?', [$uuid]);
        if (!$eventBean) {
            throw new InvalidValidationException('Invalid Event UUID');
        }
        return $eventBean->export();
    }

    public static function destroy(string $uuid): bool
    {
        $eventBean = R::findOne(self::TABLE_NAME, 'event_uuid = ?', [$uuid]);
        if (!$eventBean) {
            throw new InvalidValidationException('Invalid Event UUID');
        }
        return (bool) R::trash($eventBean);
    }
}