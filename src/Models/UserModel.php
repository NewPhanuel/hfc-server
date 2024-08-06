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
        if ($userEntity->getAddress())
            $userBean['address'] = $userEntity->getAddress();
        if ($userEntity->getDepartment())
            $userBean['department'] = $userEntity->getDepartment();
        if ($userEntity->getDepartmentLevel())
            $userBean['department_level'] = $userEntity->getDepartmentLevel();
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

    public static function authorise(string $email, string $password): ?array
    {
        $userBean = R::findOne(self::TABLE_NAME, 'email = ?', [$email]);
        if (!$userBean)
            return null;

        if (!password_verify($password, $userBean['password']))
            return null;
        return $userBean->export();
    }

    public static function setSessionToken(string $token, string $uuid): bool
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);

        $userBean['session_token'] = $token;
        $userBean['last_session_time'] = date(self::DATE_TIME_FORMAT);
        try {
            R::store($userBean);
            R::close();
            return true;
        } catch (RedBeanSQLException) {
            return false;
        }
    }

    public static function clearSessionToken(string $uuid): bool
    {
        $userBean = R::findOne(self::TABLE_NAME, 'user_uuid = ?', [$uuid]);
        if (!$userBean)
            return false;

        $userBean['session_token'] = null;

        try {
            R::store($userBean);
            R::close();
            return true;
        } catch (RedBeanSQLException $e) {
            return false;
        }
    }

    public static function validateToken(string $token): array|false
    {
        $userBean = R::findOne(self::TABLE_NAME, 'session_token = ?', [$token]);
        if (!$userBean) {
            return false;
        }
        return $userBean->export();
    }

    public static function verify(int $code, string $email): array|false
    {
        $userBean = R::findOne(self::TABLE_NAME, 'email = ?', [$email]);
        if (!$userBean) {
            return false;
        }
        if ($code !== (int) $userBean['verification_code'])
            return false;

        $userBean['verification_code'] = null;
        $userBean['is_verified'] = 'true';
        $userBean['email_verified_at'] = date(self::DATE_TIME_FORMAT);
        R::store($userBean);
        R::close();
        return $userBean->export();
    }

    public static function findByEmail(string $email): array|false
    {
        $userBean = R::findOne(self::TABLE_NAME, 'email = ?', [$email]);
        if (!$userBean) {
            return false;
        }
        return $userBean->export();
    }

    public static function saveCode(string $email, int $verificationCode): bool
    {
        $userBean = R::findOne(self::TABLE_NAME, 'email = ?', [$email]);
        if (!$userBean) {
            return false;
        }

        $userBean['verification_code'] = $verificationCode;
        R::store($userBean);
        R::close();
        return true;
    }

    public static function saveOTP(string $email, int $OTP): bool
    {
        $userBean = R::findOne(self::TABLE_NAME, 'email = ?', [$email]);
        if (!$userBean) {
            return false;
        }

        $userBean['OTP'] = $OTP;
        R::store($userBean);
        R::close();
        return true;
    }

    public static function changePassword(UserEntity $userEntity): bool
    {
        $userBean = R::findOne(self::TABLE_NAME, 'email = ?', [$userEntity->getEmail()]);
        if (!$userBean) {
            return false;
        }

        if ($userEntity->getOTP() !== (int) $userBean['OTP'])
            return false;

        $userBean['password'] = $userEntity->getPassword();
        $userBean['last_password_reset'] = $userEntity->getLastPasswordReset();
        $userBean['OTP'] = null;
        R::store($userBean);
        R::close();
        return true;
    }
}