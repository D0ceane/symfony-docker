<?php

namespace App\Enum;

enum Role: string
{
    case COACH = 'coach';
    case PLAYER = 'player';
    case REPLACEMENT = 'remplaçant';
    case DEFAULT = 'default';

    public static function fromValue(string $value): Role
    {
        return match ($value) {
            'coach' => Role::COACH,
            'player' => Role::PLAYER,
            'remplaçant' => Role::REPLACEMENT,
            default => Role::DEFAULT,
        };
    }

}
