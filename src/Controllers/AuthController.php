<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Services\MailService;
use DevPhanuel\Core\Middleware\LoginMiddleware;
use DevPhanuel\Core\Middleware\PasswordResetMiddleware;
use DevPhanuel\Core\Middleware\VerificationRequestMiddleware;
use DevPhanuel\Models\Entity\AccountEntity;
use DevPhanuel\Models\AuthModel;
use DevPhanuel\Models\UserModel;
use DevPhanuel\Validation\SchemaValidation;
use DevPhanuel\Models\Entity\UserEntity;
use Firebase\JWT\JWT;
use PH7\JustHttp\StatusCode;
use Ramsey\Uuid\Nonstandard\Uuid;

class AuthController
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private SchemaValidation $SchemaValidation;

    public function __construct()
    {
        $this->SchemaValidation = new SchemaValidation();
    }

    public function login(array $params): void
    {
        $data = $params['data'];

        if (!$this->SchemaValidation->validateLoginSchema($data))
            response(StatusCode::FORBIDDEN, errorMessage('Validation Error', 'User data does not follow validation rules', StatusCode::FORBIDDEN));

        $user = AuthModel::authorise(email: $data->email, password: $data->password);

        if (!is_array($user))
            response(StatusCode::BAD_REQUEST, errorMessage('Credential Error', 'Invalid Credentials', StatusCode::BAD_REQUEST));

        (new LoginMiddleware($user))->handle();
        $username = "{$user['firstname']} {$user['lastname']}";

        $payload = [
            "iss" => $_ENV['APP_URL'],
            "iat" => time(),
            "exp" => time() + $_ENV['JWT_EXP'],
            "data" => [
                "uuid" => $user['user_uuid'],
                "name" => $username,
                "email" => $user['email'],
                "role" => $user['role'],
                "isRestricted" => $user['is_restricted'],
                "canAccessQuiz" => $user['can_access_quiz'],
            ],
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], $_ENV['JWT_ALGO']);
        if (AuthModel::setSessionToken($jwt, $user['user_uuid']))
            response(StatusCode::OK, successMessage("User successfully logged in", ["token" => $jwt]));

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Server Error', 'Could not store session token', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function logout(array $params): void
    {
        $user = $params['user'];

        if (AuthModel::clearSessionToken($user->data->uuid))
            response(StatusCode::OK, successMessage("User successfully logged out"));

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Server Error', 'Could not delete session token', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function store(array $params): void
    {
        $data = $params['data'];

        if (!$this->SchemaValidation->validateUserSchema($data)) {
            response(StatusCode::FORBIDDEN, errorMessage('Validation Error', 'User data does not follow validation rules', StatusCode::FORBIDDEN));
        }

        $data->userUuid = (string) Uuid::uuid4();
        $data->createdAt = date(self::DATE_TIME_FORMAT);
        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $userEntity = new UserEntity();
        $userEntity->setUserUuid($data->userUuid)->setEmail($data->email)->setPassword($data->password)
            ->setFirstname($data->firstname)->setLastname($data->lastname)->setGender($data->gender)->setDob($data->dob)
            ->setCreatedAt($data->createdAt)->setUpdatedAt($data->updatedAt);

        $accountEntity = new AccountEntity();
        $accountEntity->setUserUuid($data->userUuid)->setCreatedAt($data->createdAt)
            ->setUpdatedAt($data->updatedAt);

        $verificationCode = MailService::sendCode($data->email);
        if (!$verificationCode)
            response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Mailer Error', 'Verification code could not be sent.', StatusCode::INTERNAL_SERVER_ERROR));

        $userEntity->setVerificationCode($verificationCode);

        $payload = [
            "iss" => $_ENV['APP_URL'],
            "iat" => time(),
            "exp" => time() + 600,
            "data" => [
                "email" => $data->email,
            ],
        ];

        if (UserModel::store($userEntity, $accountEntity)) {
            $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], $_ENV['JWT_ALGO']);
            AuthModel::setSessionToken($jwt, $data->userUuid);
            response(StatusCode::CREATED, successMessage('User successfully registered', ['token' => $jwt]));
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'User could not be created', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function verify(array $params): void
    {
        $code = $params['data']->code;
        $email = $params['user']->data->email;

        if (!$this->SchemaValidation->validateCode($code))
            response(StatusCode::FORBIDDEN, errorMessage('Invalid Validation Error', 'The code must be six digits', StatusCode::FORBIDDEN));

        $user = AuthModel::verify(email: $email, code: (int) $code);

        if (!is_array($user))
            response(StatusCode::BAD_REQUEST, errorMessage('Credential Error', 'Verification code is incorrect or expired', StatusCode::BAD_REQUEST));

        $username = "{$user['firstname']} {$user['lastname']}";

        $payload = [
            "iss" => $_ENV['APP_URL'],
            "iat" => time(),
            "exp" => time() + $_ENV['JWT_EXP'],
            "data" => [
                "uuid" => $user['user_uuid'],
                "name" => $username,
                "email" => $user['email'],
                "role" => $user['role'],
                "isRestricted" => $user['is_restricted'],
                "canAccessQuiz" => $user['can_access_quiz'],
            ],
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], $_ENV['JWT_ALGO']);
        if (AuthModel::setSessionToken($jwt, $user['user_uuid']))
            response(StatusCode::OK, successMessage("User is verified successfully", ["token" => $jwt]));

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Server Error', 'Could not store session token', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function resend(array $params): void
    {
        $email = $params['data']->email;

        if (!$this->SchemaValidation->validateEmail($email))
            response(StatusCode::FORBIDDEN, errorMessage('Invalid Validation Error', 'Invalid Email Address', StatusCode::FORBIDDEN));

        $user = UserModel::findByEmail($email);
        if ($user === false)
            response(StatusCode::NOT_FOUND, errorMessage('Not Found', 'User is not yet registered', StatusCode::NOT_FOUND));

        (new VerificationRequestMiddleware($user))->handle();

        $verificationCode = MailService::sendCode($email);
        if (!$verificationCode)
            response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Mailer Error', 'Verification code could not be sent.', StatusCode::INTERNAL_SERVER_ERROR));

        $payload = [
            "iss" => $_ENV['APP_URL'],
            "iat" => time(),
            "exp" => time() + 600,
            "data" => [
                "email" => $email,
            ],
        ];

        if (AuthModel::saveCode($email, $verificationCode)) {
            $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], $_ENV['JWT_ALGO']);
            AuthModel::setSessionToken($jwt, $user['user_uuid']);
            response(StatusCode::CREATED, successMessage('Verification code sent successfully', ['token' => $jwt]));
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'For some reason, verification code could not be saved on the server', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function request(array $params): void
    {
        $email = $params['data']->email;

        if (!$this->SchemaValidation->validateEmail($email))
            response(StatusCode::FORBIDDEN, errorMessage('Invalid Validation Error', 'Invalid Email Address', StatusCode::FORBIDDEN));

        $user = UserModel::findByEmail($email);
        if ($user === false)
            response(StatusCode::NOT_FOUND, errorMessage('Not Found', 'User is not yet registered', StatusCode::NOT_FOUND));

        // (new LoginMiddleware($user))->handle();

        $OTP = MailService::sendCode($email, true);
        if (!$OTP)
            response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Mailer Error', 'OTP could not be sent.', StatusCode::INTERNAL_SERVER_ERROR));

        $payload = [
            "iss" => $_ENV['APP_URL'],
            "iat" => time(),
            "exp" => time() + 600,
            "data" => [
                "email" => $email,
            ],
        ];

        if (AuthModel::saveOTP($email, $OTP)) {
            $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], $_ENV['JWT_ALGO']);
            AuthModel::setSessionToken($jwt, $user['user_uuid']);
            response(StatusCode::CREATED, successMessage('OTP sent successfully', ['token' => $jwt]));
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'For some reason, OTP could not be saved on the server', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function reset(array $params): void
    {
        $data = $params['data'];
        $email = $params['user']->data->email;

        if (!$this->SchemaValidation->validatePasswordReset($data))
            response(StatusCode::FORBIDDEN, errorMessage('Invalid Validation Error', 'Invalid Email or OTP', StatusCode::FORBIDDEN));

        $user = UserModel::findByEmail($email);
        if ($user === false)
            response(StatusCode::NOT_FOUND, errorMessage('Not Found', 'User is not yet registered', StatusCode::NOT_FOUND));

        (new PasswordResetMiddleware($user, (int) $data->otp))->handle();

        $userEntity = new UserEntity();
        $userEntity->setEmail($email)->setPassword($data->password)->setOTP((int) $data->otp)
            ->setLastPasswordReset(date(self::DATE_TIME_FORMAT));
        $username = $user['firstname'] . ' ' . $user['lastname'];

        if (AuthModel::changePassword($userEntity)) {
            AuthModel::clearSessionToken($user['user_uuid']);
            response(StatusCode::OK, successMessage('Password changed successfully. Log in to continue', [
                "name" => $username,
                "email" => $email,
            ]));
        }
        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'For some reason, the password could not be changed', StatusCode::INTERNAL_SERVER_ERROR));
    }
}