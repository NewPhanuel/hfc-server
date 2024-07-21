<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\UserModel;
use DevPhanuel\Validation\SchemaValidation;
use DevPhanuel\Models\Entity\UserEntity;
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

    public function index(): void
    {
        $users = UserModel::index();
        if (!$users) {
            response(StatusCode::NO_CONTENT, errorMessage('No Content', 'No content found in the server', StatusCode::NO_CONTENT));
            return;
        }
        foreach ($users as $user) {
            unset($user['id']);
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
        $userEntity->setUserUuid($data->userUuid)->setFirstname($data->firstname)->setLastname($data->lastname)
            ->setEmail($data->email)->setPhone($data->phone)->setPassword($data->password)->setCreatedAt($data->createdAt)
            ->setUpdatedAt($data->updatedAt);

        UserModel::store($userEntity);
        response(StatusCode::CREATED, successMessage('User successfully created', $data));
    }

    public function update(array $params): void
    {
        $data = $params['data'];
        $uuid = $params['uuid'];

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
        if (isset($data->dob))
            $userEntity->setDob($data->dob);
        if (isset($data->worker_status))
            $userEntity->setWorkerStatus($data->worker_status);
        if (isset($data->department))
            $userEntity->setDepartment($data->department);
        if (isset($data->workers_certificate))
            $userEntity->setWorkersCertificate($data->workers_certificate);
        $userEntity->setUpdatedAt($data->updatedAt);

        $user = UserModel::update($uuid, $userEntity);
        unset($user['id']);
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