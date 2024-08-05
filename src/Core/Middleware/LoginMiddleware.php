<?php
declare(strict_types=1);

namespace DevPhanuel\Core\Middleware;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PH7\JustHttp\StatusCode;

class LoginMiddleware
{

    public function __construct(public readonly array $user)
    {
    }

    public function handle(): void
    {
        if ($this->isSignedIn())
            response(StatusCode::BAD_REQUEST, errorMessage('Access Error', 'User is already signed in', StatusCode::BAD_REQUEST));

        if ($this->userIsRestricted())
            response(StatusCode::BAD_REQUEST, errorMessage('Access Error', 'User is Restricted', StatusCode::BAD_REQUEST));

        if (!$this->isVerified())
            response(StatusCode::BAD_REQUEST, errorMessage('Access Error', 'User is yet to verify email address', StatusCode::BAD_REQUEST));
    }

    public function userIsRestricted(): bool
    {
        return $this->user['is_restricted'] === 'true';
    }

    public function isVerified(): bool
    {
        return $this->user['is_verified'] === 'true';
    }

    public function isSignedIn(): bool
    {
        $result = true;

        if (!isset($this->user['session_token']))
            $result = false;

        if (isset($this->user['session_token'])) {
            try {
                JWT::decode($this->user['session_token'], new Key($_ENV['JWT_KEY'], $_ENV['JWT_ALGO']));
            } catch (Exception $e) {
                $result = false;
            }
        }
        return $result;
    }
}