<?php

namespace Database\Seeders;

use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CharacterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();

        $characters = [
            [
                'name' => 'André Cajurana',
                'role' => 'Personagem',
                'type' => 'Principal',
                'description' => 'Personagem central da história, envolvido em diversos eventos importantes',
                'history' => 'Tem uma história complexa envolvendo sua família e suas origens. Passou por diversos desafios e transformações ao longo da narrativa.',
                'personality' => 'Determinado, resiliente e com forte senso de justiça. Demonstra capacidade de liderança e adaptação em situações difíceis.',
                'goals' => 'Busca descobrir a verdade sobre sua família e resolver os conflitos pendentes.',
                'fears' => 'Medo de falhar com aqueles que dependem dele e de repetir erros do passado.',
                'notes' => 'Personagem com arco de desenvolvimento significativo ao longo da história.',
                'user_id' => $admin->id
            ],
            [
                'name' => 'Carla Brandão',
                'role' => 'Personagem',
                'type' => 'Principal',
                'description' => 'Personagem fundamental na trama, com conexões importantes com outros personagens',
                'history' => 'Tem um passado marcante que influencia suas decisões presentes. Sua história se entrelaça com eventos cruciais da narrativa.',
                'personality' => 'Forte, determinada e estratégica. Demonstra grande capacidade de análise e tomada de decisão.',
                'goals' => 'Busca proteger seus interesses e os daqueles que são importantes para ela.',
                'fears' => 'Teme perder o controle das situações e não conseguir proteger as pessoas próximas.',
                'notes' => 'Personagem com forte influência no desenvolvimento da trama.',
                'user_id' => $admin->id
            ],
            [
                'name' => 'Ana Prata',
                'role' => 'Personagem',
                'type' => 'Principal',
                'description' => 'Personagem chave com papel crucial no desenvolvimento da história',
                'history' => 'Possui uma trajetória complexa que se conecta com os principais eventos da trama.',
                'personality' => 'Perspicaz, resiliente e determinada. Demonstra grande habilidade em lidar com situações complexas.',
                'goals' => 'Busca resolver questões do passado e estabelecer um novo futuro.',
                'fears' => 'Receio de que seu passado afete negativamente seu presente e futuro.',
                'notes' => 'Personagem com desenvolvimento significativo e importantes revelações ao longo da história.',
                'user_id' => $admin->id
            ]
        ];

        foreach ($characters as $character) {
            Character::create($character);
        }
    }
}
