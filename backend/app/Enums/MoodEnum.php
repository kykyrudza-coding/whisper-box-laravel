<?php

declare(strict_types=1);

namespace App\Enums;

enum MoodEnum: string
{
    case HAPPY = 'happy';
    case SAD = 'sad';
    case NEUTRAL = 'neutral';
    case ANGRY = 'angry';
    case CONFUSED = 'confused';

    public function emoji(): string
    {
        return match ($this) {
            self::HAPPY => '😄',
            self::SAD => '😢',
            self::NEUTRAL => '😐',
            self::ANGRY => '😡',
            self::CONFUSED => '🤯',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::HAPPY => 'Щасливий',
            self::SAD => 'Сумний',
            self::NEUTRAL => 'Нейтральний',
            self::ANGRY => 'Злий',
            self::CONFUSED => 'Розгублений',
        };
    }
}
