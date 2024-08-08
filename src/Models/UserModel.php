<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\Entity\AccountEntity;
use DevPhanuel\Models\Entity\UserEntity;
use RedBeanPHP\RedException\SQL as RedBeanSQLException;
use RedBeanPHP\R;

final class UserModel
{
    private const TABLE_NAME = 'users';
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public static function index(): array
    {
        return R::findAll(self::TABLE_NAME);
    }

    public static function store(UserEntity $userEntity, AccountEntity $accountEntity): int|string|null
    {
        $existingUserbyEmail = R::findOne(self::TABLE_NAME, 'email = ?', [$userEntity->getEmail()]);
        if ($existingUserbyEmail) {
            throw new RedBeanSQLException('A user with this email already exists');
        }

        $userBean = R::dispense(self::TABLE_NAME);
        $userBean['user_uuid'] = $userEntity->getUserUuid();
        $userBean['email'] = $userEntity->getEmail();
        $userBean['password'] = $userEntity->getPassword();
        $userBean['role'] = $userEntity->getRole()->value;
        $userBean['is_restricted'] = $userEntity->getIsRestricted()->value;
        $userBean['can_access_quiz'] = $userEntity->getCanAccessQuiz()->value;
        $userBean['quiz_attempt'] = $userEntity->getQuizAttempt();
        $userBean['scores'] = $userEntity->getScores();
        $userBean['verification_code'] = $userEntity->getVerificationCode();
        $userBean['is_verified'] = $userEntity->getIsVerified()->value;
        $userBean['created_at'] = $userEntity->getCreatedAt();
        $userBean['updated_at'] = $userEntity->getUpdatedAt();
        $beanId = R::store($userBean);

        if (AccountModel::store($accountEntity)) {
            R::close();
            return $beanId;
        }

        R::trash($userBean);
        R::close();
        return null;
    }

    public static function update(string $uuid, UserEntity $userEntity, AccountEntity $accountEntity): mixed
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
        if ($userEntity->getPhone())
            $userBean['phone'] = $userEntity->getPhone();
        if ($userEntity->getRole())
            $userBean['role'] = $userEntity->getRole()->value;
        if ($userEntity->getIsRestricted())
            $userBean['is_restricted'] = $userEntity->getIsRestricted()->value;
        if ($userEntity->getCanAccessQuiz())
            $userBean['can_access_quiz'] = $userEntity->getCanAccessQuiz()->value;
        if ($userEntity->getQuizAttempt())
            $userBean['quiz_attempt'] = $userEntity->getQuizAttempt();
        if ($userEntity->getScores())
            $userBean['scores'] = $userEntity->getScores();
        $userBean['updated_at'] = $userEntity->getUpdatedAt();
        $account = AccountModel::update($uuid, $accountEntity);

        if (is_array($account)) {
            $user = $userBean->export();
            $user['account'] = $account;
            R::store($userBean);
            R::close();
            return $user;
        }
        R::close();
        return null;
    }

    public static function show(string $uuid): array
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        $userAccount = AccountModel::show($uuid);
        if (!$userBean && !$userAccount) {
            throw new InvalidValidationException('Invalid User UUID');
        }
        unset($userAccount['id']);
        $user = $userBean->export();
        $user['account'] = $userAccount;
        return $user;
    }

    public static function get(string $uuid): array
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

    public static function findByEmail(string $email): array|false
    {
        $userBean = R::findOne(self::TABLE_NAME, 'email = ?', [$email]);
        if (!$userBean) {
            return false;
        }
        return $userBean->export();
    }
}