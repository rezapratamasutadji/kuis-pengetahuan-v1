<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@kuis.test'],
            [
                'name' => 'Admin Kuis',
                'password' => 'password',
            ],
        );

        $this->call([
            ParticipantSeeder::class,
            QuizSeeder::class,
        ]);
    }
}
