<?php
declare(strict_types=1);

namespace DevPhanuel\Core;

class Authorize
{
    /**
     * Checks if the current user is the owner of the resource
     *
     * @param integer $resourseId
     * @return boolean
     */
    public static function isOwner(int $resourseId): bool
    {
        // TODO Implement Jwt and UUID checks here
        return false;
    }
}