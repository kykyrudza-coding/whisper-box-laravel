<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MoodEnum;
use App\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        Note::query()
            ->create([
                'title' => 'Сьогодні дивний день',
                'content' => 'Я не знаю, що роблю, але продовжую...',
                'mood' => MoodEnum::CONFUSED,
            ]);

        Note::query()
            ->create([
                'title' => 'Нормально пішло',
                'content' => 'Зробив половину задач — вже добре',
                'mood' => MoodEnum::NEUTRAL,
            ]);

        Note::query()
            ->create([
                'title' => 'Є прогрес',
                'content' => 'Сьогодні реально продуктивний день',
                'mood' => MoodEnum::HAPPY,
            ]);

        Note::factory()
            ->count(10)
            ->create();
    }
}
