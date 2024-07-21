<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\EventModel;
use DevPhanuel\Validation\SchemaValidation;
use DevPhanuel\Models\Entity\EventEntity;
use PH7\JustHttp\StatusCode;
use Ramsey\Uuid\Nonstandard\Uuid;

class EventsController
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private SchemaValidation $SchemaValidation;

    public function __construct()
    {
        $this->SchemaValidation = new SchemaValidation();
    }

    public function index(): void
    {
        $events = EventModel::index();
        if (!$events) {
            response(StatusCode::NO_CONTENT, errorMessage('No Content', 'No content found in the server', StatusCode::NO_CONTENT));
            return;
        }
        foreach ($events as $event) {
            unset($event['id']);
        }
        response(StatusCode::OK, successMessage('All Events on the server', $events));
        return;
    }

    public function store(array $params): void
    {
        $data = $params['data'];

        if (!$this->SchemaValidation->validateEventSchema($data)) {
            throw new InvalidValidationException('Schema does not follow validation rules');
        }

        $data->eventUuid = (string) Uuid::uuid4();
        $data->createdAt = date(self::DATE_TIME_FORMAT);
        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $eventEntity = new EventEntity();
        $eventEntity->setEventUuid($data->eventUuid)->setName($data->event_name)->setDescription($data->description)
            ->setDate($data->event_date)->setImage($data->event_image)->setCreatedAt($data->createdAt)
            ->setUpdatedAt($data->updatedAt);

        EventModel::store($eventEntity);
        response(StatusCode::CREATED, successMessage('Event successfully created', $data));
    }

    public function show(array $params): void
    {
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid Event UUID');
        }

        $event = EventModel::show($uuid);
        unset($event['id']);
        response(StatusCode::OK, successMessage('Event successfully retrieved from the server', $event));
        return;
    }

    public function update(array $params): void
    {
        $data = $params['data'];
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid))
            throw new InvalidValidationException('Invalid Event UUID');

        if (!$this->SchemaValidation->validateEventSchemaForUpdate($data))
            throw new InvalidValidationException('Schema does not follow validation rules');

        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $eventEntity = new EventEntity();

        if (isset($data->event_name))
            $eventEntity->setName($data->event_name);
        if (isset($data->description))
            $eventEntity->setDescription($data->description);
        if (isset($data->event_date))
            $eventEntity->setDate($data->event_date);
        if (isset($data->event_image))
            $eventEntity->setImage($data->event_image);
        $eventEntity->setUpdatedAt($data->updatedAt);

        $event = EventModel::update($uuid, $eventEntity);
        unset($event['id']);
        response(StatusCode::OK, successMessage('Event successfully updated', $event));
    }

    public function destroy(array $params): void
    {
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid Event UUID');
        }

        if (EventModel::destroy($uuid)) {
            response(StatusCode::OK, successMessage('Event deleted successfully'));
            return;
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('SQLError', 'For some reason, the event could not be deleted', StatusCode::INTERNAL_SERVER_ERROR));
        return;
    }
}