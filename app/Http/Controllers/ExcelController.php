<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Scene;
use App\Models\ExcelData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ExcelController extends Controller
{
    public function index()
    {
        $excelData = ExcelData::where('user_id', Auth::id())->get();
        return view('excel.index', compact('excelData'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'project_id' => 'required|exists:projects,id'
        ]);

        try {
            $file = $request->file('file');
            if ($file && $file->isValid()) {
                // Carrega os dados do Excel
                $rawData = Excel::toArray([], $file)[0];
                
                // Remove linhas vazias do início e fim
                while (!empty($rawData) && empty(array_filter($rawData[0]))) {
                    array_shift($rawData);
                }
                while (!empty($rawData) && empty(array_filter($rawData[count($rawData) - 1]))) {
                    array_pop($rawData);
                }

                // Define o número total de atos
                $totalActs = 30;
                $totalColumns = $totalActs + 1; // +1 para a coluna do nome do personagem

                // Processa os dados do Excel
                $data = [];

                // Processa cada linha
                foreach ($rawData as $index => $row) {
                    // Pula linhas sem nome de personagem
                    if (empty(trim((string)($row[0] ?? '')))) {
                        continue;
                    }

                    // Garante que a linha tenha todas as 31 colunas (nome + 30 atos)
                    $processedRow = array_pad($row, $totalColumns, '');
                    
                    // Processa cada célula mantendo o conteúdo original
                    for ($i = 0; $i < $totalColumns; $i++) {
                        // Preserva o conteúdo original, mesmo que pareça vazio
                        $processedRow[$i] = isset($row[$i]) ? $row[$i] : '';
                    }

                    $data[] = $processedRow;
                }

                Log::info('Análise inicial:', [
                    'total_rows' => count($data),
                    'total_columns' => $totalColumns,
                    'sample_row_columns' => isset($data[0]) ? count($data[0]) : 0
                ]);

                // Inicia uma transação para garantir consistência dos dados
                DB::beginTransaction();
                try {
                    // Limpa os dados antigos deste projeto
                    DB::table('character_scene')
                        ->join('scenes', 'scenes.id', '=', 'character_scene.scene_id')
                        ->where('scenes.project_id', $request->input('project_id'))
                        ->delete();

                    Scene::where('project_id', $request->input('project_id'))->delete();
                    Character::where('project_id', $request->input('project_id'))->delete();

                    // Cria todas as cenas/atos primeiro
                    $scenes = [];
                    for ($i = 1; $i <= $totalActs; $i++) {
                        $scene = Scene::create([
                            'title' => "Ato {$i}",
                            'project_id' => $request->input('project_id'),
                            'user_id' => Auth::id(),
                            'description' => '',
                            'duration' => 0,
                            'order' => $i
                        ]);
                        $scenes[$i] = $scene->id;
                    }

                    // Array para armazenar os diálogos a serem inseridos
                    $dialoguesToInsert = [];
                    $charactersProcessed = 0;

                    // Processa cada linha (personagem)
                    foreach ($data as $rowIndex => $row) {
                        $characterName = trim((string)$row[0]);
                        
                        // Cria o personagem
                        $character = Character::create([
                            'name' => $characterName,
                            'project_id' => $request->input('project_id'),
                            'user_id' => Auth::id(),
                            'role' => 'Personagem',
                            'type' => 'Principal'
                        ]);

                        $dialoguesForCharacter = [];
                        // Prepara os diálogos para inserção em lote
                        for ($i = 1; $i <= $totalActs; $i++) {
                            $dialogue = isset($row[$i]) ? $row[$i] : '';
                            
                            $dialoguesToInsert[] = [
                                'character_id' => $character->id,
                                'scene_id' => $scenes[$i],
                                'dialogue' => $dialogue,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];

                            $dialoguesForCharacter[$i] = $dialogue;
                        }

                        // Insere os diálogos em lotes de 100
                        if (count($dialoguesToInsert) >= 100) {
                            DB::table('character_scene')->insert($dialoguesToInsert);
                            $dialoguesToInsert = [];
                        }

                        $charactersProcessed++;
                        Log::info("Personagem processado:", [
                            'row' => $rowIndex + 1,
                            'name' => $characterName,
                            'id' => $character->id,
                            'total_acts_with_content' => count(array_filter($dialoguesForCharacter))
                        ]);
                    }

                    // Insere os diálogos restantes
                    if (!empty($dialoguesToInsert)) {
                        DB::table('character_scene')->insert($dialoguesToInsert);
                    }

                    // Salva os dados do Excel para referência
                    $excelData = ExcelData::create([
                        'user_id' => Auth::id(),
                        'project_id' => $request->input('project_id'),
                        'file_name' => $file->getClientOriginalName(),
                        'headers' => json_encode([]),
                        'data' => json_encode($data)
                    ]);

                    DB::commit();
                    Log::info('Importação concluída com sucesso', [
                        'total_characters' => $charactersProcessed,
                        'total_scenes' => count($scenes),
                        'total_dialogues_expected' => $charactersProcessed * $totalActs,
                        'total_columns_processed' => $totalColumns
                    ]);

                    return redirect()->route('excel.show', $excelData)
                        ->with('success', 'Arquivo importado com sucesso!');

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Erro durante a transação:', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            return redirect()->back()
                ->with('error', 'O arquivo enviado é inválido.');

        } catch (\Exception $e) {
            Log::error('Erro na importação:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erro ao importar arquivo: ' . $e->getMessage());
        }
    }

    private function extractActNumber($sceneTitle)
    {
        if (preg_match('/Ato (\d+)/i', $sceneTitle, $matches)) {
            return (int) $matches[1];
        }
        return 1; // Retorna 1 como padrão se não encontrar o número do ato
    }

    public function show(ExcelData $excelData)
    {
        // Define o número total de atos
        $totalActs = 30;

        // Carrega os dados do Excel
        $data = json_decode($excelData->data, true);

        // Log dos dados brutos do Excel para debug
        Log::info('Dados brutos do Excel:', [
            'total_rows' => count($data),
            'total_columns' => isset($data[0]) ? count($data[0]) : 0
        ]);

        // Carrega os personagens do banco de dados
        $characters = Character::where('project_id', $excelData->project_id)
            ->orderBy('name')
            ->get();

        // Carrega as cenas do banco de dados
        $scenes = Scene::where('project_id', $excelData->project_id)
            ->orderBy('order')
            ->get();

        // Carrega os diálogos do banco de dados com todos os campos necessários
        $characterScenes = DB::table('character_scene AS cs')
            ->select('cs.*', 's.title', 's.order', 'c.name as character_name')
            ->join('scenes AS s', 's.id', '=', 'cs.scene_id')
            ->join('characters AS c', 'c.id', '=', 'cs.character_id')
            ->where('s.project_id', $excelData->project_id)
            ->orderBy('s.order')
            ->get();

        // Matriz final para exibição
        $finalMatrix = [];

        // Primeiro, processa os dados do banco de dados
        foreach ($characters as $character) {
            $characterActs = array_fill(1, $totalActs, '');

            // Adiciona os diálogos do banco de dados
            foreach ($characterScenes as $cs) {
                if ($cs->character_name === $character->name) {
                    $actNumber = $cs->order;
                    if ($actNumber >= 1 && $actNumber <= $totalActs) {
                        $characterActs[$actNumber] = $cs->dialogue;
                    }
                }
            }

            $finalMatrix[$character->id] = [
                'name' => $character->name,
                'acts' => $characterActs
            ];

            // Log para debug dos diálogos do personagem
            Log::info("Diálogos do personagem {$character->name}:", [
                'total_acts' => count($characterActs),
                'acts_with_content' => count(array_filter($characterActs))
            ]);
        }

        // Depois, verifica se há personagens no Excel que não estão no banco
        foreach ($data as $row) {
            $characterName = trim((string)$row[0]);
            if (empty($characterName)) continue;

            // Verifica se o personagem já está na matriz final
            $exists = false;
            foreach ($finalMatrix as $charData) {
                if ($charData['name'] === $characterName) {
                    $exists = true;
                    break;
                }
            }

            // Se não existe, adiciona com os dados do Excel
            if (!$exists) {
                $acts = array_fill(1, $totalActs, '');
                for ($i = 1; $i <= $totalActs; $i++) {
                    if (isset($row[$i])) {
                        $acts[$i] = trim((string)$row[$i]);
                    }
                }

                $finalMatrix['temp_' . md5($characterName)] = [
                    'name' => $characterName,
                    'acts' => $acts
                ];

                // Log para debug dos dados do Excel
                Log::info("Dados do Excel para o personagem {$characterName}:", [
                    'total_acts' => count($acts),
                    'acts_with_content' => count(array_filter($acts))
                ]);
            }
        }

        // Ordena a matriz final por nome do personagem
        uasort($finalMatrix, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Log para debug da matriz final
        Log::info('Matriz final:', [
            'total_characters' => count($finalMatrix),
            'characters' => array_map(function($char) {
                return [
                    'name' => $char['name'],
                    'total_acts' => count($char['acts']),
                    'acts_with_content' => count(array_filter($char['acts']))
                ];
            }, $finalMatrix)
        ]);

        return view('excel.show', [
            'excelData' => $excelData,
            'timelineMatrix' => $finalMatrix,
            'maxActs' => $totalActs
        ]);
    }

    public function destroy(ExcelData $excelData)
    {
        $excelData->delete();
        return redirect()->route('excel.index')
            ->with('success', 'Arquivo excluído com sucesso!');
    }
}
