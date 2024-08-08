<?php
declare(strict_types=1);

namespace DevPhanuel\Models;

use DevPhanuel\Exception\InvalidValidationException;
use DevPhanuel\Models\Entity\AccountEntity;
use DevPhanuel\Models\Entity\UserEntity;
use RedBeanPHP\RedException\SQL as RedBeanSQLException;
use RedBeanPHP\R;

final class AuthModel
{
    private const TABLE_NAME = 'users';
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

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