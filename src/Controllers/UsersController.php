<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\Entity\AccountEntity;
use DevPhanuel\Models\UserModel;
use DevPhanuel\Validation\SchemaValidation;
use DevPhanuel\Models\Entity\UserEntity;
use Firebase\JWT\JWT;
use PH7\JustHttp\StatusCode;
use Ramsey\Uuid\Nonstandard\Uuid;

class UsersController
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

        if (!$this->SchemaValidation->validateUserSchema($data))
            response(StatusCode::BAD_REQUEST, errorMessage('Validation Error', 'User data does not follow validation rules', StatusCode::BAD_REQUEST));

        $user = UserModel::authorise(email: $data->email, password: $data->password);

        if (!is_array($user))
            response(StatusCode::BAD_REQUEST, errorMessage('Validation Error', 'Invalid Credentials', StatusCode::BAD_REQUEST));
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
        if (UserModel::setSessionToken($jwt, $user['user_uuid']))
            response(StatusCode::OK, successMessage("{$username} is successfully logged in", ["token" => $jwt]));

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Server Error', 'Could not store session token', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function logout(array $params): void
    {
        $user = $params['user'];

        if (UserModel::clearSessionToken($user->data->uuid))
            response(StatusCode::OK, successMessage("User successfully logged out"));

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Server Error', 'Could not delete session token', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function index(): void
    {
        $users = UserModel::index();
        if (!$users) {
            response(StatusCode::NO_CONTENT, errorMessage('No Content', 'No content found in the server', StatusCode::NO_CONTENT));
            return;
        }
        foreach ($users as $user) {
            unset($user['id']);
            unset($user['session_token']);
        }
        response(StatusCode::OK, successMessage('All Users on the server', $users));
        return;
    }

    public function store(array $params): void
    {
        $data = $params['data'];

        if (!$this->SchemaValidation->validateUserSchema($data)) {
            throw new InvalidValidationException('Schema does not follow validation rules');
        }

        $data->userUuid = (string) Uuid::uuid4();
        $data->createdAt = date(self::DATE_TIME_FORMAT);
        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $userEntity = new UserEntity();
        $userEntity->setUserUuid($data->userUuid)->setEmail($data->email)->setPassword($data->password)
            ->setCreatedAt($data->createdAt)
            ->setUpdatedAt($data->updatedAt);

        $accountEntity = new AccountEntity();
        $accountEntity->setUserUuid($data->userUuid);

        if (UserModel::store($userEntity, $accountEntity))
            response(StatusCode::CREATED, successMessage('User successfully created', $data));

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'User successfully created', StatusCode::INTERNAL_SERVER_ERROR));
    }

    public function update(array $params): void
    {
        $data = $params['data'];
        $uuid = $params['user']->data->uuid;

        if (!$this->SchemaValidation->validateUserSchemaForUpdate($data)) {
            throw new InvalidValidationException('Schema does not follow validation rules 1');
        }

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid User UUID');
        }

        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $userEntity = new UserEntity();
        if (isset($data->firstname))
            $userEntity->setFirstname($data->firstname);
        if (isset($data->lastname))
            $userEntity->setLastname($data->lastname);
        if (isset($data->profile_pics))
            $userEntity->setProfilePics($data->profile_pics);
        if (isset($data->gender))
            $userEntity->setGender($data->gender);
        if (isset($data->phone))
            $userEntity->setPhone($data->phone);
        if (isset($data->dob))
            $userEntity->setDob($data->dob);
        if (isset($data->address))
            $userEntity->setAddress($data->address);
        if (isset($data->department))
            $userEntity->setDepartment($data->department);
        if (isset($data->department_level))
            $userEntity->setDepartmentLevel($data->department_level);
        $userEntity->setUpdatedAt($data->updatedAt);

        $user = UserModel::update($uuid, $userEntity);
        unset($user['id']);
        unset($user['session_token']);
        response(StatusCode::OK, successMessage('User successfully updated', $user));
    }

    public function show(array $params): void
    {
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid User UUID');
        }

        $user = UserModel::show($uuid);
        unset($user['id']);
        unset($user['session_token']);
        response(StatusCode::OK, successMessage('User successfully retrieved from the server', $user));
        return;
    }

    public function destroy(array $params): void
    {
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid User UUID');
        }

        if (UserModel::destroy($uuid)) {
            response(StatusCode::OK, successMessage('User deleted successfully'));
            return;
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('SQLError', 'For some reason, the user could not be deleted', StatusCode::INTERNAL_SERVER_ERROR));
        return;
    }
}