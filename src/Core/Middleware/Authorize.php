<?php
declare(strict_types=1);

namespace DevPhanuel\Core\Middleware;


class Authorize
{
    /**
     * Checks if user is authenticated
     *
     * @return boolean
     */
    public function isAuthenticated(): bool
    {
        // TODO Implement JWT authentication check
        return true;
    }
    /**
     * Handles a request
     *
     * @param string $role
     * @return void
     */
    public function handle(string $role): void
    {
        if ($role === 'guest' && $this->isAuthenticated()) {
            redirect('/');
            return;
        } else if ($role === 'auth' && !$this->isAuthenticated()) {
            redirect('/auth/login');
            return;
        }
    }
}