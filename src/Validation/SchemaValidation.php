<?php
declare(strict_types=1);

namespace DevPhanuel\Validation;

use Respect\Validation\Validator as validate;

class SchemaValidation
{
    private const PASSWORD_REGEX = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/';
    private const MAX_NAME_LENGTH = 20;
    private const MIN_NAME_LENGTH = 2;

    public function validateUserSchema(object $data): bool
    {
        $schemaValidator = validate::attribute('firstname', validate::stringType()->length(self::MIN_NAME_LENGTH, self::MAX_NAME_LENGTH))
            ->attribute('lastname', validate::stringType()->length(self::MIN_NAME_LENGTH, self::MAX_NAME_LENGTH))
            ->attribute('email', validate::email())
            ->attribute('phone', validate::phone())
            ->attribute('password', validate::regex(self::PASSWORD_REGEX));
        return $schemaValidator->validate($data);
    }

    public function validateUserSchemaForUpdate(object $data): bool
    {
        $schemaValidator = validate::attribute('firstname', validate::stringType()->length(self::MIN_NAME_LENGTH, self::MAX_NAME_LENGTH), mandatory: false)
            ->attribute('lastname', validate::stringType()->length(self::MIN_NAME_LENGTH, self::MAX_NAME_LENGTH), mandatory: false)
            ->attribute('profile_pics', validate::stringType(), mandatory: false)
            ->attribute('gender', validate::stringType(), mandatory: false)
            ->attribute('dob', validate::date(), mandatory: false)
            ->attribute('worker_status', validate::stringType(), mandatory: false)
            ->attribute('department', validate::stringType(), mandatory: false)
            ->attribute('workers_certificate', validate::stringType(), mandatory: false);
        return $schemaValidator->validate($data);
    }

    public function validateUuid(string $uuid): bool
    {
        return validate::uuid(4)->validate($uuid);
    }

    public function validateEventSchema(object $data): bool
    {
        $schemaValidator = validate::attribute('event_name', validate::stringType())
            ->attribute('description', validate::stringType())
            ->attribute('event_date', validate::stringType())
            ->attribute('event_image', validate::stringType());
        return $schemaValidator->validate($data);
    }

    public function validateEventSchemaForUpdate(object $data): bool
    {
        $schemaValidator = validate::attribute('event_name', validate::stringType(), mandatory: false)
            ->attribute('description', validate::stringType(), mandatory: false)
            ->attribute('event_date', validate::stringType(), mandatory: false)
            ->attribute('event_image', validate::stringType(), mandatory: false);
        return $schemaValidator->validate($data);
    }
}