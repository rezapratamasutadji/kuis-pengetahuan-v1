<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $participants = [
            ['name' => 'Peserta 1', 'display_order' => 1, 'is_active' => true],
            ['name' => 'Peserta 2', 'display_order' => 2, 'is_active' => true],
            ['name' => 'Peserta 3', 'display_order' => 3, 'is_active' => true],
            ['name' => 'Peserta 4', 'display_order' => 4, 'is_active' => true],
            ['name' => 'Peserta 5', 'display_order' => 5, 'is_active' => true],
        ];

        foreach ($participants as $participant) {
            Participant::query()->updateOrCreate(
                ['name' => $participant['name']],
                $participant,
            );
        }
    }
}
