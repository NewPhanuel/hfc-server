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
        $schemaValidator = validate::attribute('email', validate::email())
            ->attribute('firstname', validate::stringType()->length(self::MIN_NAME_LENGTH, self::MAX_NAME_LENGTH))
            ->attribute('lastname', validate::stringType()->length(self::MIN_NAME_LENGTH, self::MAX_NAME_LENGTH))
            ->attribute('gender', validate::stringType(), mandatory: false)
            ->attribute('dob', validate::date(), mandatory: false)
            ->attribute('password', validate::regex(self::PASSWORD_REGEX));
        return $schemaValidator->validate($data);
    }

    public function validateLoginSchema(object $data): bool
    {
        $schemaValidator = validate::attribute('email', validate::email())
            ->attribute('password', validate::stringType()->length(6));
        return $schemaValidator->validate($data);
    }
    public function validateCode(string $code): bool
    {
        return validate::digit()->length(6, 6)->validate($code);
    }
    public function validateEmail(string $email): bool
    {
        return validate::email()->validate($email);
    }
    public function validatePasswordReset(object $data): bool
    {
        $schemaValidator = validate::attribute('otp', validate::digit()->length(6, 6))
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
            ->attribute('phone', validate::phone(), mandatory: false)
            ->attribute('role', validate::stringType(), mandatory: false)
            ->attribute('is_restricted', validate::stringType(), mandatory: false)
            ->attribute('can_access_quiz', validate::stringType(), mandatory: false)
            ->attribute('quiz_attempt', validate::stringType(), mandatory: false)
            ->attribute('scores', validate::stringType(), mandatory: false)
            ->key('account', validate::key('email', validate::email(), mandatory: false)
                ->key('password', validate::regex(self::PASSWORD_REGEX), mandatory: false)
                ->key('is_funded', validate::stringType(), mandatory: false)
                ->key('total_funding', validate::stringType(), mandatory: false)
                ->key('total_earning', validate::stringType(), mandatory: false)
                ->key('earning_balance', validate::stringType(), mandatory: false)
                ->key('remitted_payment', validate::stringType(), mandatory: false)
                ->key('guarantor_name', validate::stringType(), mandatory: false)
                ->key('guarantor_phone', validate::stringType(), mandatory: false)
                ->key('bank_name', validate::stringType(), mandatory: false)
                ->key('acct_number', validate::stringType(), mandatory: false)
                ->key('acct_name', validate::stringType(), mandatory: false)
                ->key('address', validate::stringType(), mandatory: false)
                ->key('department', validate::stringType(), mandatory: false)
                ->key('department_level', validate::stringType(), mandatory: false)
                ->key('is_deactivated', validate::stringType(), mandatory: false), mandatory: false);
        return $schemaValidator->validate($data);
    }

    public function validateUuid(string $uuid): bool
    {
        return validate::uuid(4)->validate($uuid);
    }

    public function validateBlogSchema(object $data): bool
    {
        $schemaValidator = validate::attribute('blog_name', validate::stringType())
            ->attribute('body', validate::stringType())
            ->attribute('blog_image', validate::stringType());
        return $schemaValidator->validate($data);
    }

    public function validateBlogSchemaForUpdate(object $data): bool
    {
        $schemaValidator = validate::attribute('blog_name', validate::stringType(), mandatory: false)
            ->attribute('body', validate::stringType(), mandatory: false)
            ->attribute('blog_image', validate::stringType(), mandatory: false);
        return $schemaValidator->validate($data);
    }
}