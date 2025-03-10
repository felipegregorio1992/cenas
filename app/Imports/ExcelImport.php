<?php

namespace App\Imports;

use App\Models\Character;
use App\Models\ExcelData;
use App\Models\Scene;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;

class ExcelImport implements ToCollection, WithHeadingRow, ToArray
{
    use Importable;

    protected $data;
    protected $headers;
    protected $fileName;
    protected $userId;

    public function __construct($fileName, $userId)
    {
        $this->fileName = $fileName;
        $this->userId = $userId;
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        // Armazena os dados
        $this->data = $rows->toArray();

        // Salva no banco de dados
        ExcelData::create([
            'user_id' => $this->userId,
            'file_name' => $this->fileName,
            'headers' => array_keys($rows->first()->toArray()),
            'data' => $this->data
        ]);

        // Importa os personagens e suas cenas
        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            $values = array_values($rowArray);
            
            if (!empty($values[0])) { // Se o nome não estiver vazio
                // Cria o personagem
                $character = Character::create([
                    'user_id' => $this->userId,
                    'name' => $values[0], // Nome do personagem
                    'role' => 'Personagem',
                    'type' => 'Principal',
                    'description' => 'Personagem da história'
                ]);

                // Processa cada coluna como uma cena
                foreach ($values as $sceneIndex => $sceneDescription) {
                    // Pula a primeira coluna (nome do personagem)
                    if ($sceneIndex > 0 && !empty($sceneDescription)) {
                        // Cria uma nova cena
                        $scene = Scene::create([
                            'user_id' => $this->userId,
                            'title' => "Ato " . $sceneIndex,
                            'description' => $sceneDescription,
                            'order' => $sceneIndex,
                            'duration' => 60 // Duração padrão de 60 minutos
                        ]);

                        // Vincula o personagem à cena
                        $character->scenes()->attach($scene->id);
                    }
                }
            }
        }
    }

    public function getData()
    {
        return collect($this->data);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function array(array $rows)
    {
        return $rows;
    }
}
