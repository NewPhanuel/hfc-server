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

    public function login(array $params): void
    {
        $data = $params['data'];

        if (!$this->SchemaValidation->validateUserSchema($data))
            response(StatusCode::BAD_REQUEST, errorMessage('Validation Error', 'User data does not follow validation rules', StatusCode::BAD_REQUEST));

        $user = UserModel::authorise(email: $data->email, password: $data->password);

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
        if (UserModel::setSessionToken($jwt, $user['user_uuid']))
            response(StatusCode::OK, successMessage("User successfully logged in", ["token" => $jwt]));

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
            UserModel::setSessionToken($jwt, $data->userUuid);
            response(StatusCode::CREATED, successMessage('User successfully registered', ['token' => $jwt]));
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'User could not be created', StatusCode::INTERNAL_SERVER_ERROR));
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
        if (isset($data->address))
            $userEntity->setAddress($data->address);
        if (isset($data->department))
            $userEntity->setDepartment($data->department);
        if (isset($data->department_level))
            $userEntity->setDepartmentLevel($data->department_level);
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
        if (isset($data->address))
            $userEntity->setAddress($data->address);
        if (isset($data->department))
            $userEntity->setDepartment($data->department);
        if (isset($data->department_level))
            $userEntity->setDepartmentLevel($data->department_level);
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

    public function verify(array $params): void
    {
        $code = $params['data']->code;
        $email = $params['user']->data->email;

        if (!$this->SchemaValidation->validateCode($code))
            response(StatusCode::FORBIDDEN, errorMessage('Invalid Validation Error', 'The code must be six digits', StatusCode::FORBIDDEN));

        $user = UserModel::verify(email: $email, code: (int) $code);

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
        if (UserModel::setSessionToken($jwt, $user['user_uuid']))
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

        if (UserModel::saveCode($email, $verificationCode)) {
            $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], $_ENV['JWT_ALGO']);
            UserModel::setSessionToken($jwt, $user['user_uuid']);
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

        if (UserModel::saveOTP($email, $OTP)) {
            $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], $_ENV['JWT_ALGO']);
            UserModel::setSessionToken($jwt, $user['user_uuid']);
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

        if (UserModel::changePassword($userEntity)) {
            UserModel::clearSessionToken($user['user_uuid']);
            response(StatusCode::OK, successMessage('Password changed successfully. Log in to continue', [
                "name" => $username,
                "email" => $email,
            ]));
        }

        response(StatusCode::INTERNAL_SERVER_ERROR, errorMessage('Internal Server Error', 'For some reason, the password could not be changed', StatusCode::INTERNAL_SERVER_ERROR));
    }
}