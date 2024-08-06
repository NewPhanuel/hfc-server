<?php
declare(strict_types=1);

namespace DevPhanuel\Core\Middleware;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PH7\JustHttp\StatusCode;

class PasswordResetMiddleware extends LoginMiddleware
{
    public function __construct(
        public readonly array $user,
        public readonly int $OTP
    ) {
    }

    public function handle(): void
    {
        if ($this->userIsRestricted())
            response(StatusCode::BAD_REQUEST, errorMessage('Access Error', 'User is Restricted', StatusCode::BAD_REQUEST));

        if (!$this->isVerified())
            response(StatusCode::BAD_REQUEST, errorMessage('Access Error', 'User is yet to verify email address', StatusCode::BAD_REQUEST));

        if (!$this->correctOTP())
            response(StatusCode::BAD_REQUEST, errorMessage('OTP Error', 'OTP is not correct', StatusCode::BAD_REQUEST));

        if ($this->OTPExpired())
            response(StatusCode::BAD_REQUEST, errorMessage('OTP Error', 'OTP is expired or not valid', StatusCode::BAD_REQUEST));
    }

    public function correctOTP(): bool
    {
        if ((int) $this->user['otp'] !== $this->OTP)
            return false;
        return true;
    }

    public function OTPExpired(): bool
    {
        try {
            JWT::decode($this->user['session_token'], new Key($_ENV['JWT_KEY'], $_ENV['JWT_ALGO']));
            return false;
        } catch (Exception) {
            return true;
        }
    }
}
