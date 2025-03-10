<?php

namespace Database\Seeders;

use App\Models\Character;
use App\Models\Scene;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SceneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        $joao = Character::where('name', 'João')->first();
        $maria = Character::where('name', 'Maria')->first();
        $pedro = Character::where('name', 'Pedro')->first();

        $scenes = [
            [
                'title' => 'Cena 1: O Chamado',
                'description' => 'João recebe um chamado misterioso de Maria.',
                'duration' => 15,
                'order' => 1,
                'characters' => [
                    $joao->id => ['dialogue' => 'Quem está me chamando?'],
                    $maria->id => ['dialogue' => 'Venha, jovem. Seu destino o aguarda.']
                ]
            ],
            [
                'title' => 'Cena 2: O Encontro',
                'description' => 'João encontra Maria e descobre seu destino.',
                'duration' => 20,
                'order' => 2,
                'characters' => [
                    $joao->id => ['dialogue' => 'Não posso acreditar no que estou ouvindo.'],
                    $maria->id => ['dialogue' => 'É sua missão impedir os planos de Pedro.']
                ]
            ],
            [
                'title' => 'Cena 3: A Conspiração',
                'description' => 'Pedro revela seus planos malignos.',
                'duration' => 25,
                'order' => 3,
                'characters' => [
                    $pedro->id => ['dialogue' => 'Ninguém poderá me impedir agora!']
                ]
            ],
        ];

        foreach ($scenes as $sceneData) {
            $characters = $sceneData['characters'] ?? [];
            unset($sceneData['characters']);
            
            $scene = Scene::create($sceneData + ['user_id' => $admin->id]);
            
            foreach ($characters as $characterId => $pivot) {
                $scene->characters()->attach($characterId, $pivot);
            }
        }
    }
}
