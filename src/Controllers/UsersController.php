<?php
declare(strict_types=1);

namespace DevPhanuel\Controllers;

use DevPhanuel\Core\MailService;
use DevPhanuel\Core\Middleware\LoginMiddleware;
use DevPhanuel\Core\Middleware\PasswordResetMiddleware;
use DevPhanuel\Core\Middleware\VerificationRequestMiddleware;
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
        $accountEntity = new AccountEntity();

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
        if (isset($data->account)) {
            if (isset($data->account->email))
                $accountEntity->setEmail($data->account->email);
            if (isset($data->account->password))
                $accountEntity->setPassword($data->account->password);
            if (isset($data->account->guarantor_name))
                $accountEntity->setGuarantorName($data->account->guarantor_name);
            if (isset($data->account->guarantor_phone))
                $accountEntity->setGuarantorPhone($data->account->guarantor_phone);
            if (isset($data->account->bank_name))
                $accountEntity->setBankName($data->account->bank_name);
            if (isset($data->account->acct_number))
                $accountEntity->setAcctNumber($data->account->acct_number);
            if (isset($data->account->acct_name))
                $accountEntity->setAcctName($data->account->acct_name);
            if (isset($data->account->address))
                $accountEntity->setAddress($data->account->address);
            if (isset($data->account->department))
                $accountEntity->setDepartment($data->account->department);
            if (isset($data->account->department_level))
                $accountEntity->setDepartmentLevel($data->account->department_level);
        }
        $accountEntity->setUpdatedAt($data->updatedAt);
        $userEntity->setUpdatedAt($data->updatedAt);

        $user = UserModel::update($uuid, $userEntity, $accountEntity);
        if ($user === null)
            response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'User could not be updated', StatusCode::INTERNAL_SERVER_ERROR));

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

    public function getUser(array $params): void
    {
        $uuid = $params['user']->data->uuid;

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid User UUID');
        }

        $user = UserModel::show($uuid);
        unset($user['id']);
        unset($user['session_token']);
        unset($user['verification_code']);
        unset($user['password']);
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

    public function updateByAdmin(array $params): void
    {
        $data = $params['data'];
        $uuid = $params['uuid'];

        if (!$this->SchemaValidation->validateUserSchemaForUpdate($data)) {
            throw new InvalidValidationException('Schema does not follow validation rules');
        }

        if (!$this->SchemaValidation->validateUuid($uuid)) {
            throw new InvalidValidationException('Invalid User UUID');
        }

        $data->updatedAt = date(self::DATE_TIME_FORMAT);

        $userEntity = new UserEntity();
        $accountEntity = new AccountEntity();

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
        if (isset($data->role))
            $userEntity->setRole($data->role);
        if (isset($data->is_restricted))
            $userEntity->setIsRestricted($data->is_restricted);
        if (isset($data->can_access_quiz))
            $userEntity->setCanAccessQuiz($data->can_access_quiz);
        if (isset($data->quiz_attempt))
            $userEntity->setQuizAttempt($data->quiz_attempt);
        if (isset($data->scores))
            $userEntity->setScores($data->scores);
        if (isset($data->account)) {
            if (isset($data->account->email))
                $accountEntity->setEmail($data->account->email);
            if (isset($data->account->password))
                $accountEntity->setPassword($data->account->password);
            if (isset($data->account->is_funded))
                $accountEntity->setIsFunded($data->account->is_funded);
            if (isset($data->account->total_funding))
                $accountEntity->setTotalFunding($data->account->total_funding);
            if (isset($data->account->total_earning))
                $accountEntity->setTotalEarning($data->account->total_earning);
            if (isset($data->account->earning_balance))
                $accountEntity->setEarningBalance($data->account->earning_balance);
            if (isset($data->account->remitted_payment))
                $accountEntity->setRemittedPayment($data->account->remitted_payment);
            if (isset($data->account->guarantor_name))
                $accountEntity->setGuarantorName($data->account->guarantor_name);
            if (isset($data->account->guarantor_phone))
                $accountEntity->setGuarantorPhone($data->account->guarantor_phone);
            if (isset($data->account->bank_name))
                $accountEntity->setBankName($data->account->bank_name);
            if (isset($data->account->acct_number))
                $accountEntity->setAcctNumber($data->account->acct_number);
            if (isset($data->account->acct_name))
                $accountEntity->setAcctName($data->account->acct_name);
            if (isset($data->account->address))
                $accountEntity->setAddress($data->account->address);
            if (isset($data->account->department))
                $accountEntity->setDepartment($data->account->department);
            if (isset($data->account->department_level))
                $accountEntity->setDepartmentLevel($data->account->department_level);
            if (isset($data->account->is_deactivated))
                $accountEntity->setIsDeactivated($data->account->is_deactivated);
        }
        $accountEntity->setUpdatedAt($data->updatedAt);
        $userEntity->setUpdatedAt($data->updatedAt);

        $user = UserModel::update($uuid, $userEntity, $accountEntity);
        if ($user === null)
            response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'User could not be updated', StatusCode::INTERNAL_SERVER_ERROR));

        unset($user['id']);
        unset($user['session_token']);
        response(StatusCode::OK, successMessage('User successfully updated', $user));
    }
}