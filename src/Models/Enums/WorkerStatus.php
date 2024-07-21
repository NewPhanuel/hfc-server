<?php
declare(strict_types=1);

namespace DevPhanuel\Models\Enums;

enum WorkerStatus: int
{
    case NOT_TRAINED = 1;
    case IN_TRAINING = 2;
    case TRAINED = 3;

    public function status(): string
    {
        return match ($this) {
            self::NOT_TRAINED => 'not trained',
            self::IN_TRAINING => 'in training',
            self::TRAINED => 'trained',
            default => null
        };
    }
}