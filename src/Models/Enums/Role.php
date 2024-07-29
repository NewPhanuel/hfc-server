<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Enums;

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}