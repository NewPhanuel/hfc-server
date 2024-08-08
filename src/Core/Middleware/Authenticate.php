<?php
declare(strict_types=1);

namespace DevPhanuel\Core\Middleware;

use DevPhanuel\Models\AuthModel;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PH7\JustHttp\StatusCode;

class Authenticate
{
    /**
     * Checks if user is authenticated
     *
     * @return ?object
     */
    public function isAuthenticated(): ?object
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization']))
            return null;

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        $user = AuthModel::validateToken($token);
        if ($user === false)
            return null;

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_KEY'], $_ENV['JWT_ALGO']));
        } catch (Exception $e) {
            return null;
        }

        if (isset($decoded->data->uuid))
            if ($decoded->data->uuid !== $user['user_uuid'])
                return null;
        return $decoded;
    }

    public function isAdmin(?object $user): bool
    {
        if (!$user)
            return false;
        if ($user->data->role !== 'admin')
            return false;
        return true;
    }

    /**
     * Handles a request
     *
     * @param string $role
     * @return void
     */
    public function handle(string $role): ?object
    {

        $user = $this->isAuthenticated();
        if ($role === 'guest' && $user !== null) {
            response(StatusCode::UNAUTHORIZED, errorMessage('Not Authorized', "Logout to access this route", StatusCode::UNAUTHORIZED));
            return null;
        }

        if ($role === 'auth' && $user === null) {
            response(StatusCode::UNAUTHORIZED, errorMessage('Not Authorized', "Login to access this route", StatusCode::UNAUTHORIZED));
            return null;
        }

        if ($role === 'admin') {
            if (!$this->isAdmin($user)) {
                response(StatusCode::UNAUTHORIZED, errorMessage('Not Authorized', "Login as an admin to access this route", StatusCode::UNAUTHORIZED));
                return null;
            }
        }

        if ($role === 'new' && $user === null) {
            response(StatusCode::UNAUTHORIZED, errorMessage('Not Authorized', "Create an account to access this route", StatusCode::UNAUTHORIZED));
            return null;
        }

        if ($role === 'new' && isset($user->data->role)) {
            response(StatusCode::UNAUTHORIZED, errorMessage('Not Authorized', "Only unverified can access this route", StatusCode::UNAUTHORIZED));
            return null;
        }

        return $user;
    }
}