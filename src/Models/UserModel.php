<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\Entity\UserEntity;
use RedBeanPHP\RedException\SQL as RedBeanSQLException;
use RedBeanPHP\R;

final class UserModel
{
    private const TABLE_NAME = 'users';

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function store(UserEntity $userEntity): int|string
    {
        $existingUserbyEmail = R::findOne(self::TABLE_NAME, 'email = ?', [$userEntity->getEmail()]);
        if ($existingUserbyEmail) {
            throw new RedBeanSQLException('A user with this email already exists');
        }

        $existingUserbyPhone = R::findOne(self::TABLE_NAME, 'phone = ?', [$userEntity->getPhone()]);
        if ($existingUserbyPhone) {
            throw new RedBeanSQLException('A user with this phone number already exists');
        }

        $userBean = R::dispense(self::TABLE_NAME);
        $userBean['user_uuid'] = $userEntity->getUserUuid();
        $userBean['firstname'] = $userEntity->getFirstname();
        $userBean['lastname'] = $userEntity->getLastname();
        $userBean['email'] = $userEntity->getEmail();
        $userBean['phone'] = $userEntity->getPhone();
        $userBean['password'] = $userEntity->getPassword();
        $userBean['created_at'] = $userEntity->getCreatedAt();
        $userBean['updated_at'] = $userEntity->getUpdatedAt();
        $beanId = R::store($userBean);
        R::close();
        return $beanId;
    }

    public static function update(string $uuid, UserEntity $userEntity): mixed
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        if (!$userBean) {
            throw new InvalidValidationException('Invalid User UUID');
        }

        if ($userEntity->getFirstname())
            $userBean['firstname'] = $userEntity->getFirstname();
        if ($userEntity->getLastname())
            $userBean['lastname'] = $userEntity->getLastname();
        if ($userEntity->getProfilePics())
            $userBean['profile_pics'] = $userEntity->getProfilePics();
        if ($userEntity->getGender())
            $userBean['gender'] = $userEntity->getGender()->value;
        if ($userEntity->getDob())
            $userBean['dob'] = $userEntity->getDob();
        if ($userEntity->getWorkerStatus())
            $userBean['worker_status'] = $userEntity->getWorkerStatus()->status();
        if ($userEntity->getDepartment())
            $userBean['department'] = $userEntity->getDepartment();
        if ($userEntity->getWorkersCertificate())
            $userBean['worker_certificate'] = $userEntity->getWorkersCertificate();
        if ($userEntity->getUpdatedAt())
            $userBean['updated_at'] = $userEntity->getUpdatedAt();
        R::store($userBean);
        R::close();
        return $userBean;
    }

    public static function show(string $uuid): array
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        if (!$userBean) {
            throw new InvalidValidationException('Invalid User UUID');
        }
        return $userBean->export();
    }

    public static function destroy(string $uuid): bool
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        if (!$userBean) {
            throw new InvalidValidationException('Invalid User UUID');
        }
        return (bool) R::trash($userBean);
    }
}